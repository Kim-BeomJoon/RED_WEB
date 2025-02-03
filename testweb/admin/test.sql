-- 데이터베이스 생성
CREATE DATABASE admin_web;
USE admin_web;

-- 사용자 계정 정보 테이블 생성
CREATE TABLE UserAccounts (
    AccountID INT AUTO_INCREMENT PRIMARY KEY, -- 계정 ID (기본 키)
    Username VARCHAR(50) NOT NULL UNIQUE,    -- 계정명 (고유값)
    PasswordHash VARCHAR(255) NOT NULL,      -- 비밀번호 (해시 저장 권장)
    FullName VARCHAR(100) NOT NULL,          -- 이름
    Email VARCHAR(100) NOT NULL UNIQUE,      -- 이메일 (고유값)
    Gender ENUM('Male', 'Female', 'Other') NOT NULL, -- 성별
    DateOfBirth DATE NOT NULL,               -- 생년월일
    PhoneNumber VARCHAR(15) NOT NULL UNIQUE, -- 전화번호 (고유값)
    CreatedAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP, -- 생성 일시
    UpdatedAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP -- 수정 일시
);

-- 샘플 데이터 삽입
INSERT INTO UserAccounts (Username, PasswordHash, FullName, Email, Gender, DateOfBirth, PhoneNumber)
VALUES
('user1', 'hashed_password_1', 'Alice Kim', 'alice@example.com', 'Female', '1990-01-01', '010-1234-5678'),
('user2', 'hashed_password_2', 'Bob Lee', 'bob@example.com', 'Male', '1985-05-15', '010-2345-6789');