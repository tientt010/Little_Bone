<?php

namespace core;

use PDO;
use PDOException;

require_once ROOT_PATH . '/config/Database.php';

abstract class BaseModel
{
    protected $db;
    protected $table;
    protected $primaryKey = 'id';
    protected $fillable = [];
    protected $timestamps = true;

    protected $queryCache = [];

    protected $errors = [];

    public function __construct()
    {
        try {
            $this->db = \Config\Database::getInstance()->getConnection();
            $this->db->exec("SET NAMES utf8mb4");
        } catch (\PDOException $e) {

            throw new \Exception('Database connection failed');
        }
    }

    // lấy tất cả bản ghi trong bảng
    public function getAll($columns = ['*'])
    {
        try {
            $columnsString = $columns[0] === '*' ? '*' : implode(', ', $columns);

            $sql = "SELECT {$columnsString} FROM {$this->table}";
            $stmt = $this->db->prepare($sql);
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {

            return [];
        }
    }


    // Thêm bản ghi mới
    public function create($data)
    {
        try {
            if (!is_array($data)) {

                return false;
            }

            if ($this->timestamps) {
                $now = date('Y-m-d H:i:s');
                $data['created_at'] = $now;
            }

            $columns = implode(', ', array_keys($data));
            $placeholders = ':' . implode(', :', array_keys($data));

            $sql = "INSERT INTO {$this->table} ({$columns}) VALUES ({$placeholders})";
            $stmt = $this->db->prepare($sql);

            foreach ($data as $key => $value) {
                $stmt->bindValue(":{$key}", $value);
            }

            $result = $stmt->execute();

            return $result ? $this->db->lastInsertId() : false;
        } catch (PDOException $e) {

            return false;
        }
    }

    // Cập nhật bản ghi
    public function update($id, $data)
    {
        try {
            if ($this->timestamps) {
                $data['updated_at'] = date('Y-m-d H:i:s');
            }

            $setClauses = [];
            foreach ($data as $key => $value) {
                $setClauses[] = "{$key} = :{$key}";
            }

            $sql = "UPDATE {$this->table} SET " . implode(', ', $setClauses) . " WHERE {$this->primaryKey} = :id";
            $stmt = $this->db->prepare($sql);

            foreach ($data as $key => $value) {
                $stmt->bindValue(":{$key}", $value);
            }
            $stmt->bindValue(':id', $id);

            return $stmt->execute();
        } catch (PDOException $e) {

            return false;
        }
    }

    // Xoá bản ghi
    public function delete($id)
    {
        try {
            $sql = "DELETE FROM {$this->table} WHERE {$this->primaryKey} = :id";
            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(':id', $id);

            return $stmt->execute();
        } catch (PDOException $e) {

            return false;
        }
    }

    // Đếm số bản ghi theo điều kiện
    public function count($conditions = [])
    {
        try {
            $sql = "SELECT COUNT(*) as count FROM {$this->table}";
            $params = [];

            if (!empty($conditions)) {
                $sql .= " WHERE ";
                $whereClauses = [];

                foreach ($conditions as $key => $value) {
                    if (is_array($value)) {
                        $operator = $value[0];
                        $val = $value[1];
                        $whereClauses[] = "{$key} {$operator} :{$key}";
                    } else {
                        $whereClauses[] = "{$key} = :{$key}";
                        $val = $value;
                    }
                    $params[$key] = $val;
                }

                $sql .= implode(' AND ', $whereClauses);
            }

            $stmt = $this->db->prepare($sql);

            foreach ($params as $key => $value) {
                $stmt->bindValue(":{$key}", $value);
            }

            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            return (int)$result['count'];
        } catch (PDOException $e) {

            return 0;
        }
    }


    // Bắt đầu Transaction
    public function beginTransaction()
    {
        return $this->db->beginTransaction();
    }

    // Commit Transaction
    public function commit()
    {
        return $this->db->commit();
    }

    // Rollback Transaction
    public function rollBack()
    {
        return $this->db->rollBack();
    }

    // Truy vấn với điều kiện WHERE
    public function where($column, $operator, $value = null)
    {
        if ($value === null) {
            $value = $operator;
            $operator = '=';
        }

        if (!isset($this->queryCache['where'])) {
            $this->queryCache['where'] = [];
        }

        $this->queryCache['where'][] = [
            'column' => $column,
            'operator' => $operator,
            'value' => $value
        ];

        return $this;
    }

    // Truy vẫn không điều kiện
    public function get($columns = ['*'])
    {
        $cols = is_array($columns) ? implode(', ', $columns) : $columns;

        $sql = "SELECT {$cols} FROM {$this->table}";
        $params = [];

        // Thêm điều kiện WHERE nếu có
        if (isset($this->queryCache['where']) && !empty($this->queryCache['where'])) {
            $sql .= " WHERE ";
            $whereClauses = [];

            foreach ($this->queryCache['where'] as $index => $condition) {
                $whereClauses[] = "{$condition['column']} {$condition['operator']} ?";
                $params[] = $condition['value'];
            }

            $sql .= implode(' AND ', $whereClauses);
        }

        // Thực hiện truy vấn
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);

        // Reset query cache
        $this->queryCache = [];

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Xác thực dữ liệu đầu vào
    protected function validate($data, $rules = [])
    {
        $valid = true;
        $this->errors = []; // Reset errors

        foreach ($rules as $field => $rule) {
            // Bỏ qua nếu field không có trong data và không bắt buộc
            if (!isset($data[$field]) && strpos($rule, 'required') === false) {
                continue;
            }

            $ruleItems = explode('|', $rule);
            $fieldValue = $data[$field] ?? '';

            foreach ($ruleItems as $ruleItem) {
                // Kiểm tra rule có params không (vd: min:6)
                if (strpos($ruleItem, ':') !== false) {
                    list($ruleName, $ruleValue) = explode(':', $ruleItem);
                } else {
                    $ruleName = $ruleItem;
                    $ruleValue = null;
                }

                // Xác thực theo loại rule
                switch ($ruleName) {
                    case 'required':
                        if (empty($fieldValue)) {
                            $this->errors[$field] = "Trường {$field} là bắt buộc";
                            $valid = false;
                        }
                        break;

                    case 'email':
                        if (!filter_var($fieldValue, FILTER_VALIDATE_EMAIL)) {
                            $this->errors[$field] = "Trường {$field} phải là email hợp lệ";
                            $valid = false;
                        }
                        break;

                    case 'min':
                        if (strlen($fieldValue) < $ruleValue) {
                            $this->errors[$field] = "Trường {$field} phải có ít nhất {$ruleValue} ký tự";
                            $valid = false;
                        }
                        break;

                    case 'max':
                        if (strlen($fieldValue) > $ruleValue) {
                            $this->errors[$field] = "Trường {$field} không được vượt quá {$ruleValue} ký tự";
                            $valid = false;
                        }
                        break;

                    case 'in':
                        $allowedValues = explode(',', $ruleValue);
                        if (!in_array($fieldValue, $allowedValues)) {
                            $this->errors[$field] = "Trường {$field} phải là một trong các giá trị: " . implode(', ', $allowedValues);
                            $valid = false;
                        }
                        break;
                }
            }
        }

        return $valid;
    }

    /**
     * Lấy danh sách lỗi
     * @return array Danh sách lỗi
     */
    public function getErrors()
    {
        return $this->errors;
    }

    // Tìm ban ghi theo ID
    public function find($id, $columns = ['*'])
    {
        $cols = is_array($columns) ? implode(', ', $columns) : $columns;

        $sql = "SELECT {$cols} FROM {$this->table} WHERE {$this->primaryKey} = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result !== false ? $result : false;
    }

    // Tìm kiếm một bản ghi duy nhất
    public function findOne($conditions = [], $columns = ['*'])
    {
        $results = $this->findWhere($conditions, $columns, null, 1);
        return !empty($results) ? $results[0] : null;
    }

    // Tìm kiếm với điều kiện WHERE, ORDER BY, LIMIT
    public function findWhere($conditions = [], $columns = ['*'], $orderBy = null, $limit = null, $offset = null)
    {
        try {
            $columnsString = $columns[0] === '*' ? '*' : implode(', ', $columns);

            $sql = "SELECT {$columnsString} FROM {$this->table}";
            $params = [];

            if (!empty($conditions)) {
                $sql .= " WHERE ";
                $whereClauses = [];

                foreach ($conditions as $key => $value) {
                    if (is_array($value)) {
                        $operator = $value[0];
                        $val = $value[1];
                        $whereClauses[] = "{$key} {$operator} :{$key}";
                    } else {
                        $whereClauses[] = "{$key} = :{$key}";
                        $val = $value;
                    }
                    $params[$key] = $val;
                }

                $sql .= implode(' AND ', $whereClauses);
            }

            if ($orderBy) {
                if (is_array($orderBy)) {
                    $orderByString = $orderBy['column'] . ' ' . $orderBy['direction'];
                    $sql .= " ORDER BY {$orderByString}";
                } else {
                    $sql .= " ORDER BY {$orderBy}";
                }
            }

            if ($limit !== null) {
                $sql .= " LIMIT :limit";
                if ($offset !== null) {
                    $sql .= " OFFSET :offset";
                }
            }

            $stmt = $this->db->prepare($sql);

            foreach ($params as $key => $value) {
                $stmt->bindValue(":{$key}", $value);
            }

            if ($limit !== null) {
                $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
                if ($offset !== null) {
                    $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
                }
            }

            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {

            return [];
        }
    }
}
