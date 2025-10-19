// Biến toàn cục cho dashboard
let bookingsTable;
let revenueChart, occupancyChart, roomTypeChart, ratingsChart;
let startDatepicker, endDatepicker;

// Chuyển đổi định dạng ngày từ dd/mm/yyyy sang yyyy-mm-dd
function convertToYMDFormat(dateStr, statsType) {
    if (!dateStr) return "";

    const parts = dateStr.split("/");

    if (parts.length === 3) {
        return `${parts[2]}-${parts[1]}-${parts[0]}`;
    }

    return dateStr;
}

document.addEventListener("DOMContentLoaded", function () {
    setTimeout(() => {
        initializeFlatpickr();
        initializeCharts();
        initializeEventHandlers();
        initializeDataTables();
    }, 500);
});

// Khởi tạo DataTables

function initializeDataTables() {
    try {
        $.fn.dataTable.ext.type.order["formatted-num-pre"] = function (data) {
            let num = data.replace(/[^\d.-]/g, "");
            return parseFloat(num) || 0;
        };

        const vietnameseLanguage = {
            emptyTable: "Không có dữ liệu",
            info: "Hiển thị _START_ đến _END_ của _TOTAL_ mục",
            infoEmpty: "Hiển thị 0 đến 0 của 0 mục",
            lengthMenu: "Hiển thị _MENU_ mục",
            loadingRecords: "Đang tải...",
            processing: "Đang xử lý...",
            search: "Tìm kiếm:",
            zeroRecords: "Không tìm thấy kết quả",
            paginate: {
                first: "Đầu tiên",
                last: "Cuối cùng",
                next: "Tiếp theo",
                previous: "Trước đó",
            },
        };

        bookingsTable = $("#bookingsTable").DataTable({
            language: vietnameseLanguage,
            pageLength: 5,
            lengthMenu: [5, 10, 15, 20, 25],
            searching: true,
            ordering: true,
            order: [[3, "desc"]],
            destroy: true,
        });
    } catch (error) {
        console.error("Lỗi khi khởi tạo DataTables:", error);
    }
}

// Khởi tạo Flatpickr cho khoảng thời gian thống kê
function initializeFlatpickr() {
    updateDateRange();

    document.querySelectorAll(".modal-body .btn.nav-link").forEach((btn) => {
        btn.addEventListener("click", function (e) {
            e.preventDefault();

            document
                .querySelectorAll(".modal-body .btn.nav-link")
                .forEach((b) => {
                    b.classList.remove("active");
                });
            this.classList.add("active");

            document.getElementById("statsType").value =
                this.getAttribute("data-value");
            updateDateRange();
        });
    });
}

// cập nhật khoảng thời gian dựa trên loại thống kê đã chọn
function updateDateRange() {
    const statsType = document.getElementById("statsType").value;
    const today = new Date();
    let startDate, endDate;

    switch (statsType) {
        case "this_week":
            startDate = getMonday(today);
            endDate = new Date(startDate);
            endDate.setDate(startDate.getDate() + 6);
            break;

        case "last_week":
            startDate = getMonday(new Date(today));
            startDate.setDate(startDate.getDate() - 7);
            endDate = new Date(startDate);
            endDate.setDate(startDate.getDate() + 6);
            break;

        case "this_month":
            startDate = new Date(today.getFullYear(), today.getMonth(), 1);
            endDate = new Date(today.getFullYear(), today.getMonth() + 1, 0);
            break;

        case "last_month":
            startDate = new Date(today.getFullYear(), today.getMonth() - 1, 1);
            endDate = new Date(today.getFullYear(), today.getMonth(), 0);
            break;

        case "this_year":
            startDate = new Date(today.getFullYear(), 0, 1);
            endDate = new Date(today.getFullYear(), 11, 31);
            break;

        case "last_year":
            startDate = new Date(today.getFullYear() - 1, 0, 1);
            endDate = new Date(today.getFullYear() - 1, 11, 31);
            break;

        default:
            startDate = new Date(today.getFullYear(), 0, 1);
            endDate = new Date(today.getFullYear(), 11, 31);
            break;
    }

    document.getElementById("startDate").value = formatDate(startDate);
    document.getElementById("endDate").value = formatDate(endDate);
}

function initializeEventHandlers() {
    const applyDateFilter = document.getElementById("applyDateFilter");
    if (applyDateFilter) {
        applyDateFilter.addEventListener("click", function () {
            const statsType = document.getElementById("statsType").value;
            const startDate = document.getElementById("startDate").value;

            const modal = bootstrap.Modal.getInstance(
                document.getElementById("dateFilterModal")
            );
            modal.hide();

            updateDashboard(statsType, startDate);
        });
    }
}

function getMonday(d) {
    d = new Date(d);
    var day = d.getDay(),
        diff = d.getDate() - day + (day == 0 ? -6 : 1);
    return new Date(d.setDate(diff));
}

// Định dạng ngay theo định dạng dd/mm/yyyy
function formatDate(date) {
    const day = date.getDate().toString().padStart(2, "0");
    const month = (date.getMonth() + 1).toString().padStart(2, "0");
    const year = date.getFullYear();
    return `${day}/${month}/${year}`;
}

// Khởi tạo biểu đồ
function initializeCharts() {
    // Lấy dữ liệu từ biến toàn cục được truyền từ PHP
    const revenueData = window.dashboardData.charts.revenue || {};
    const occupancyData = window.dashboardData.charts.occupancy || {};
    const roomTypeData = window.dashboardData.charts.room_types || {};
    const ratingsData = window.dashboardData.charts.ratings || {};

    // Biểu đồ doanh thu
    const revenueOptions = {
        series: [
            {
                name: "Doanh thu",
                data: revenueData.values || [],
            },
        ],
        chart: {
            height: 300,
            type: "area",
            toolbar: { show: true },
            zoom: { enabled: true },
        },
        dataLabels: { enabled: false },
        stroke: { curve: "smooth", width: 3 },
        colors: ["#0d6efd"],
        xaxis: {
            categories: revenueData.categories || [],
            title: { text: "Thời gian" },
        },
        tooltip: {
            y: {
                formatter: function (val) {
                    return (val / 1000000).toFixed(1) + " triệu đồng";
                },
            },
        },
        yaxis: {
            labels: {
                formatter: function (val) {
                    return (val / 1000000).toFixed(0) + "M";
                },
            },
            title: { text: "Triệu đồng" },
        },
        fill: {
            type: "gradient",
            gradient: {
                shade: "light",
                type: "vertical",
                shadeIntensity: 0.5,
                inverseColors: false,
                opacityFrom: 0.7,
                opacityTo: 0.2,
            },
        },
    };
    revenueChart = new ApexCharts(
        document.querySelector("#revenueChart"),
        revenueOptions
    );
    revenueChart.render();

    // Biểu đồ tỉ lệ lấp đầy
    const trendOptions = {
        series: [
            {
                name: "Tỉ lệ lấp đầy",
                data: occupancyData.values || [],
            },
        ],
        chart: {
            height: 300,
            type: "bar",
            toolbar: { show: true },
        },
        plotOptions: {
            bar: {
                borderRadius: 4,
                columnWidth: "60%",
                dataLabels: { position: "top" },
            },
        },
        dataLabels: {
            enabled: true,
            formatter: function (val) {
                return val.toFixed(0) + "%";
            },
            offsetY: -20,
            style: { fontSize: "12px", fontWeight: "bold" },
        },
        colors: ["#17a2b8"],
        xaxis: {
            categories: occupancyData.categories || [],
            title: { text: "Thời gian" },
        },
        yaxis: {
            min: 0,
            max: 100,
            labels: {
                formatter: function (val) {
                    return val.toFixed(0) + "%";
                },
            },
            title: { text: "Tỉ lệ lấp đầy (%)" },
        },
        tooltip: {
            y: {
                formatter: function (val) {
                    return val + "%";
                },
            },
        },
    };
    occupancyChart = new ApexCharts(
        document.querySelector("#occupancyTrend"),
        trendOptions
    );
    occupancyChart.render();

    // Biểu đồ phân bố loại phòng
    const roomTypeOptions = {
        series: roomTypeData.values || [],
        chart: {
            width: "100%",
            height: 250,
            type: "pie",
        },
        labels: roomTypeData.categories || [],
        colors: [
            "#007bff",
            "#28a745",
            "#fd7e14",
            "#17a2b8",
            "#6f42c1",
            "#20c997",
        ],
        legend: { position: "bottom" },
        responsive: [
            {
                breakpoint: 480,
                options: {
                    chart: { width: 200 },
                    legend: { position: "bottom" },
                },
            },
        ],
    };
    roomTypeChart = new ApexCharts(
        document.querySelector("#roomTypeChart"),
        roomTypeOptions
    );
    roomTypeChart.render();

    // Biểu đồ đánh giá
    const ratingsOptions = {
        series: [
            {
                name: "Điểm đánh giá",
                data: ratingsData.values || [],
            },
        ],
        chart: {
            height: 250,
            type: "line",
            toolbar: { show: true },
        },
        stroke: { width: 3, curve: "smooth" },
        markers: { size: 5, hover: { size: 7 } },
        colors: ["#ffc107"],
        xaxis: {
            categories: ratingsData.categories || [],
            title: { text: "Thời gian" },
        },
        yaxis: {
            title: { text: "Điểm đánh giá trung bình" },
            min: 0,
            max: 5,
            tickAmount: 5,
        },
        tooltip: {
            y: {
                formatter: function (val) {
                    return val + "/5 sao";
                },
            },
        },
        grid: {
            borderColor: "#e0e0e0",
            row: { colors: ["#f3f3f3", "transparent"] },
        },
    };
    ratingsChart = new ApexCharts(
        document.querySelector("#ratingsChart"),
        ratingsOptions
    );
    ratingsChart.render();
}

// Cập nhật dữ liệu biểu đồ dựa trên loại thống kê và ngày bắt đầu
function updateDashboard(statsType, startDate) {
    startDate = convertToYMDFormat(startDate);

    const formData = new FormData();
    formData.append("stats_type", statsType.slice(5));
    formData.append("start_date", startDate);

    let labelCount;
    switch (statsType) {
        case "this_week":
        case "last_week":
            labelCount = 7;
            break;
        case "this_month":
        case "last_month":
            labelCount = 10;
            break;
        case "this_year":
        case "last_year":
            labelCount = 12;
            break;
        default:
            labelCount = 7;
    }

    formData.append("label_count", labelCount);

    fetch(`${window.SITE_URL}/hotel_staff/getDashboardData`, {
        method: "POST",
        body: formData,
    })
        .then((response) => {
            if (!response.ok) {
                throw new Error("Network response was not ok");
            }
            return response.json();
        })
        .then((data) => {
            if (data.success) {
                updateCharts(data.charts);
                showToast("Dữ liệu đã được cập nhật thành công", "success");
            } else {
                throw new Error(
                    data.message || "Có lỗi xảy ra khi cập nhật dữ liệu"
                );
            }
        })
        .catch((error) => {
            console.error("Error updating dashboard:", error);
            showToast("Có lỗi xảy ra khi cập nhật dữ liệu", "danger");
        });
}

// Cập nhật biểu đồ với dữ liệu mới
function updateCharts(charts) {
    if (charts.revenue) {
        revenueChart.updateOptions({
            xaxis: { categories: charts.revenue.categories || [] },
        });
        revenueChart.updateSeries([
            {
                name: "Doanh thu",
                data: charts.revenue.values || [],
            },
        ]);
    }

    if (charts.occupancy) {
        occupancyChart.updateOptions({
            xaxis: { categories: charts.occupancy.categories || [] },
        });
        occupancyChart.updateSeries([
            {
                name: "Tỉ lệ lấp đầy",
                data: charts.occupancy.values || [],
            },
        ]);
    }

    if (charts.room_types) {
        roomTypeChart.updateOptions({
            labels: charts.room_types.categories || [],
        });
        roomTypeChart.updateSeries(charts.room_types.values || []);
    }

    if (charts.ratings) {
        ratingsChart.updateOptions({
            xaxis: { categories: charts.ratings.categories || [] },
        });
        ratingsChart.updateSeries([
            {
                name: "Điểm đánh giá",
                data: charts.ratings.values || [],
            },
        ]);
    }
}

// Hiển thị thông báo dạng toast
function showToast(message, type) {
    const toastId = "toast-" + Date.now();
    const html = `
        <div class="toast" role="alert" aria-live="assertive" aria-atomic="true" id="${toastId}">
            <div class="toast-header">
                <div class="rounded me-2 bg-${type}" style="width:20px; height:20px"></div>
                <strong class="me-auto">Thông báo</strong>
                <small>Bây giờ</small>
                <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
            <div class="toast-body">
                ${message}
            </div>
        </div>
    `;

    document
        .getElementById("toastContainer")
        .insertAdjacentHTML("beforeend", html);
    const toastElement = document.getElementById(toastId);
    const toast = new bootstrap.Toast(toastElement);
    toast.show();

    toastElement.addEventListener("hidden.bs.toast", () => {
        toastElement.remove();
    });
}
