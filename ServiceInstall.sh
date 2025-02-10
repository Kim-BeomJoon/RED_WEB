#/bin/bash
while true; do
    clear
    echo "===== 시스템 관리 메뉴 ====="
    echo "1. Red Hat"  # 수정된 부분
    echo "2. Debian"
    echo "0. 종료"
    echo "=========================="
    echo -n "원하는 메뉴를 선택하세요: "
    read choice

    case $choice in
        1)
            echo "=== Red Hat httpd, mysql, php ==="  # 수정된 부분
            sudo dnf install -y httpd mariadb-server php-mysqli  # 중복 제거
            sudo systemctl enable --now httpd mysql php-fpm
            ;;
        2)
            echo "=== Debian apache2, mysql, php ==="
            sudo apt install -y apache2 mariadb-server php-mysql php-fpm libapache2-mod-php # 중복 제거
            sudo systemctl enable --now apache2 mysql
            ;;
        0)
            echo "프로그램을 종료합니다."
            exit 0
            ;;
        *)
            echo "잘못된 선택입니다. 다시 선택해주세요."
            ;;
    esac
    
    echo
    echo "계속하려면 엔터키를 누르세요..."
    read
done