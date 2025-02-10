# SQL Injection 취약점 보안 보고서

## [취약점 개요]
- 취약점명: SQL Injection 취약점
- 위험도: 상
- 취약점 설명: 사용자 입력값이 SQL 쿼리에 직접 삽입되어 데이터베이스를 비정상적으로 조작할 수 있는 취약점

## [취약점 상세]
### 1. 발견된 문제점
- 사용자 입력값에 대한 검증 부재
- SQL 쿼리에 직접 변수 삽입
- Prepared Statement 미사용
- 에러 메시지 노출

### 2. 보안 영향
- 데이터베이스 정보 유출
- 인증 우회 가능
- 데이터 조작 및 삭제
- 관리자 권한 탈취

## [대응방안]
### 1. Prepared Statements 사용
```php
// 취약한 코드
$query = "SELECT * FROM users WHERE username = '$username'";

// 안전한 코드
$stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
$stmt->execute([$username]);
```

### 2. 입력값 검증
```php
function validateInput($input) {
    // SQL 특수문자 필터링
    if (preg_match('/[\'";\-\-]/', $input)) {
        throw new Exception('잘못된 입력값');
    }
    
    // 입력 길이 제한
    if (strlen($input) > 100) {
        throw new Exception('입력값이 너무 깁니다');
    }
    
    return htmlspecialchars($input, ENT_QUOTES, 'UTF-8');
}
```

### 3. 데이터베이스 접근 제어
```php
class Database {
    private $pdo;
    
    public function __construct() {
        try {
            $this->pdo = new PDO(
                "mysql:host=localhost;dbname=myapp;charset=utf8mb4",
                "user",
                "password",
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_EMULATE_PREPARES => false
                ]
            );
        } catch (PDOException $e) {
            error_log($e->getMessage());
            throw new Exception('데이터베이스 연결 오류');
        }
    }
}
```

## [보안 설정 예시]
### 1. PHP 설정
```ini
; php.ini
display_errors = Off
log_errors = On
error_reporting = E_ALL & ~E_NOTICE
```

### 2. MySQL 설정
```sql
-- 최소 권한 부여
GRANT SELECT, INSERT, UPDATE, DELETE ON myapp.* TO 'appuser'@'localhost';

-- 중요 테이블 접근 제한
REVOKE ALL PRIVILEGES ON mysql.* FROM 'appuser'@'localhost';
```

## [권장 모니터링]
### 1. 쿼리 로깅
```php
function logQuery($query, $params) {
    $log = date('Y-m-d H:i:s') . " | {$_SERVER['REMOTE_ADDR']} | Query: $query | Params: " . json_encode($params) . "\n";
    file_put_contents('../logs/query.log', $log, FILE_APPEND);
}
```

### 2. 보안 감사
- 비정상적인 쿼리 패턴 감지
- 대량 데이터 조회 모니터링
- 인증 우회 시도 탐지

### 3. 정기적인 점검
- 데이터베이스 로그 분석
- 권한 설정 검토
- 백업 상태 확인

## [추가 권장사항]
### 1. 에러 처리
```php
try {
    // 데이터베이스 작업
} catch (Exception $e) {
    error_log($e->getMessage());
    echo "시스템 오류가 발생했습니다.";
    // 상세 에러 메시지 숨김
}
```

### 2. 데이터베이스 보안 강화
- 정기적인 패스워드 변경
- 불필요한 데이터베이스 기능 비활성화
- 중요 데이터 암호화 저장

### 3. 개발 가이드라인
- SQL 인젝션 방지 코딩 규칙
- 보안 코드 리뷰 실시
- 정기적인 보안 교육

### 4. 백업 및 복구
- 정기적인 데이터베이스 백업
- 복구 절차 문서화
- 백업 데이터 무결성 검증
