# K.Knock 웹 공방전 게시판

경기대학교 정보보안 동아리 **K.Knock**의 웹 공방전(Web Attack/Defense) 실습을 위해 만든 PHP 기반 게시판입니다.
Apache + PHP + MySQL 환경에서 동작하며, 회원 인증과 게시판/댓글/파일 첨부 기능을 갖춘 간단한 웹 애플리케이션입니다.

> 동아리 활동 중 멘티 과정에서 학습용으로 직접 구현한 프로젝트입니다.

*A simple PHP/Apache/MySQL bulletin board built for the K.Knock security club's web attack/defense practice.*

## 주요 기능

- **회원 인증** — 회원가입 / 로그인 / 로그아웃 (`password_hash` 기반 비밀번호 해싱)
- **게시판** — 게시글 작성 / 조회 / 수정 / 삭제, 제목·작성자 검색, 최신순/오래된순 정렬
- **댓글** — 댓글 작성 / 수정 / 삭제
- **파일 첨부** — 게시글 작성 시 파일 업로드 및 다운로드
- **다중 게시판** — 독립적으로 동작하는 게시판 2개 (Board 1 / Board 2)

## 기술 스택

| 구분 | 내용 |
|------|------|
| 언어 | PHP |
| 웹 서버 | Apache |
| 데이터베이스 | MySQL / MariaDB |
| DB 연동 | PDO (Prepared Statement) |
| 프론트엔드 | HTML / CSS (순수 서버사이드 렌더링) |

## 디렉터리 구조

```
.
├── index.php              # 게시판 선택 랜딩 페이지
├── auth/                  # 인증
│   ├── login.php
│   ├── logout.php
│   └── register.php
├── board1/                # 게시판 1
│   ├── index.php          # 게시글 목록
│   ├── view.php           # 게시글 조회
│   ├── create_post.php    # 게시글 작성
│   ├── edit.php           # 게시글 수정
│   ├── delete.php         # 게시글 삭제
│   ├── comment_create.php # 댓글 작성
│   ├── comment_edit.php   # 댓글 수정
│   └── comment_delete.php # 댓글 삭제
├── board2/                # 게시판 2 (board1과 동일 구조)
├── db.php                 # DB 연결 설정 (git 미포함)
└── upload/                # 업로드 파일 저장소 (git 미포함)
```

## 실행 방법

### 1. 요구 사항

- PHP 7.x 이상
- Apache
- MySQL 또는 MariaDB

### 2. 데이터베이스 준비

`users`, `posts_board1`, `posts_board2`, `comments_board1`, `comments_board2` 테이블이 필요합니다. 예시 스키마:

```sql
CREATE TABLE users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(50) NOT NULL,
  email VARCHAR(255) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL
);

CREATE TABLE posts_board1 (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  title VARCHAR(255) NOT NULL,
  content TEXT NOT NULL,
  file_name VARCHAR(255) DEFAULT NULL,
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id)
);

CREATE TABLE comments_board1 (
  id INT AUTO_INCREMENT PRIMARY KEY,
  post_id INT NOT NULL,
  user_id INT NOT NULL,
  content TEXT NOT NULL,
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (post_id) REFERENCES posts_board1(id),
  FOREIGN KEY (user_id) REFERENCES users(id)
);

-- posts_board2, comments_board2 도 board1과 동일하게 생성
```

### 3. DB 연결 설정 (`db.php`)

프로젝트 루트에 `db.php`를 생성합니다. (보안상 git에는 포함되지 않습니다.)

```php
<?php
$host = 'localhost';
$db   = 'DB이름';
$user = 'DB사용자';
$pass = 'DB비밀번호';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$pdo = new PDO($dsn, $user, $pass, [
  PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
]);
```

### 4. 업로드 디렉터리 생성

```bash
mkdir -p upload/board1 upload/board2
```

### 5. Apache 실행

문서 루트(Document Root)에 프로젝트를 배치한 뒤 브라우저에서 접속합니다.

```
http://localhost/index.php
```

## 라이선스

동아리 학습 및 실습용으로 자유롭게 사용할 수 있습니다.
