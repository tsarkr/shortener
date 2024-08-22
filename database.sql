-- users 테이블: 사용자 정보 저장
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(255) NOT NULL UNIQUE,  -- 사용자 이름
    password VARCHAR(255) NOT NULL,  -- 비밀번호
    role ENUM('admin', 'user') DEFAULT 'user',  -- 역할 (관리자 또는 사용자)
    is_approved BOOLEAN DEFAULT FALSE,  -- 승인 여부
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP  -- 계정 생성 시간
) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- login_logs 테이블: 로그인 기록 저장
CREATE TABLE login_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,  -- users 테이블의 사용자 ID
    login_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP,  -- 로그인 시간
    logout_time TIMESTAMP NULL,  -- 로그아웃 시간
    FOREIGN KEY (user_id) REFERENCES users(id)  -- 외래 키 제약 조건
) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- urls 테이블: URL 단축 서비스 정보 저장
CREATE TABLE urls (
    id INT AUTO_INCREMENT PRIMARY KEY,
    original_url VARCHAR(2048) NOT NULL,  -- 원본 URL
    short_code VARCHAR(255) NOT NULL UNIQUE,  -- 단축된 URL 코드
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,  -- 생성 시간
    click_count INT DEFAULT 0  -- 클릭 횟수
) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- url_clicks 테이블: URL 클릭 기록 저장
CREATE TABLE url_clicks (
    id INT AUTO_INCREMENT PRIMARY KEY,
    url_id INT NOT NULL,  -- urls 테이블의 URL ID
    click_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP,  -- 클릭 시간
    referer VARCHAR(1024) NULL,  -- 참조 URL
    user_agent VARCHAR(1024) NULL,  -- 브라우저 정보
    ip_address VARCHAR(45) NULL,  -- IP 주소 (IPv4 또는 IPv6)
    CONSTRAINT fk_url_clicks_url_id FOREIGN KEY (url_id) REFERENCES urls(id) ON DELETE CASCADE  -- 외래 키 제약 조건
) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;