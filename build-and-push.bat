@echo off
echo ============================================
echo Build and Push Docker Image to Docker Hub
echo ============================================
echo.

REM Đổi tientt010 thành Docker Hub username của bạn
set DOCKER_USERNAME=tiennq04
set IMAGE_NAME=little-bone
set VERSION=latest

echo Step 1: Login to Docker Hub
echo ----------------------------
docker login

echo.
echo Step 2: Build Docker Image
echo ---------------------------
docker build -t %DOCKER_USERNAME%/%IMAGE_NAME%:%VERSION% .

echo.
echo Step 3: Push Image to Docker Hub
echo ---------------------------------
docker push %DOCKER_USERNAME%/%IMAGE_NAME%:%VERSION%

echo.
echo ============================================
echo Build and Push completed successfully!
echo Image: %DOCKER_USERNAME%/%IMAGE_NAME%:%VERSION%
echo ============================================
echo.
echo Next steps:
echo 1. SSH to your EC2 instance
echo 2. Run: docker-compose -f docker-compose.prod.yml up -d
echo ============================================
pause
