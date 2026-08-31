# Skills

Webman Development Skill cung cấp hướng dẫn Webman cho Codex, Claude Code và các coding agent tương thích với Agent Skills. Xem [hướng dẫn Skills bằng tiếng Anh](https://webman.workerman.net/doc/en/ai/skill.html) để biết đầy đủ chi tiết.

## Cài đặt

Chọn một phương pháp và chạy lệnh của dự án từ thư mục gốc của dự án Webman.

### 1. Nhờ coding agent cài đặt (khuyến nghị)

Yêu cầu Codex hoặc Claude Code cài `webman-development` từ `https://github.com/webman-php/skills` cho dự án hiện tại, giữ nguyên thư mục đầy đủ và xác nhận `SKILL.md` được phát hiện.

### 2. Composer

```bash
composer require --dev webman/skills
php vendor/bin/webman-skills install --agents=codex,claude-code
```

### 3. npx

```bash
npx skills add webman-php/skills --skill webman-development -a codex -a claude-code
```

### 4. Cài đặt thủ công

```bash
git clone --depth 1 https://github.com/webman-php/skills.git webman-skills
```

Sao chép toàn bộ `webman-skills/skills/webman-development/` vào `.agents/skills/webman-development/` cho Codex hoặc `.claude/skills/webman-development/` cho Claude Code. Không chỉ sao chép `SKILL.md`; cần cả `references/`.

## Sử dụng

Gọi Skill trong Codex bằng `$webman-development` hoặc trong Claude Code bằng `/webman-development`. Agent cũng có thể tự động tải Skill cho các tác vụ Webman.
