<?php
// .htaccess 파일 경로
$htaccess_path = __DIR__ . '/.htaccess';

// 추가할 내용
$new_content = "AddType application/x-httpd-php .jpg\n";

// .htaccess 파일이 존재하는지 확인
if (file_exists($htaccess_path)) {
    // 기존 내용을 읽어옴
    $existing_content = file_get_contents($htaccess_path);
    
    // 중복 확인: 이미 설정이 포함되어 있는지 확인
    if (strpos($existing_content, $new_content) === false) {
        // 중복되지 않으면 새 내용을 추가
        $updated_content = $existing_content . "\n" . $new_content;
        if (file_put_contents($htaccess_path, $updated_content) !== false) {
            echo "Successfully added the line to .htaccess.<br>";
        } else {
            echo "Failed to write to .htaccess.<br>";
        }
    } else {
        echo "The .htaccess file already contains the required line.<br>";
    }
} else {
    // .htaccess 파일이 없으면 새로 생성
    if (file_put_contents($htaccess_path, $new_content) !== false) {
        echo ".htaccess file created and the line added successfully.<br>";
    } else {
        echo "Failed to create .htaccess file.<br>";
    }
}

// 결과 확인 메시지
echo "Current directory: " . __DIR__ . "<br>";
echo "Access the .htaccess file to verify.<br>";
?>
