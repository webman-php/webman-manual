# Skills

Webman Development Skill ให้คำแนะนำ Webman สำหรับ Codex, Claude Code และเอเจนต์ที่รองรับ Agent Skills ดูรายละเอียดได้จาก [คู่มือ Skills ภาษาอังกฤษ](https://webman.workerman.net/doc/en/ai/skill.html)

## การติดตั้ง

เลือกหนึ่งวิธีและรันคำสั่งสำหรับโปรเจกต์จากโฟลเดอร์หลักของโปรเจกต์ Webman

### 1. ให้เอเจนต์ติดตั้งให้ (แนะนำ)

ขอให้ Codex หรือ Claude Code ติดตั้ง `webman-development` จาก `https://github.com/webman-php/skills` สำหรับโปรเจกต์ปัจจุบัน เก็บไดเรกทอรีทั้งหมด และตรวจสอบว่าค้นพบ `SKILL.md`

### 2. Composer

```bash
composer require --dev webman/skills
php vendor/bin/webman-skills install --agents=codex,claude-code
```

### 3. npx

```bash
npx skills add webman-php/skills --skill webman-development -a codex -a claude-code
```

### 4. ติดตั้งด้วยตนเอง

```bash
git clone --depth 1 https://github.com/webman-php/skills.git webman-skills
```

คัดลอกไดเรกทอรี `webman-skills/skills/webman-development/` ทั้งหมดไปที่ `.agents/skills/webman-development/` สำหรับ Codex หรือ `.claude/skills/webman-development/` สำหรับ Claude Code อย่าคัดลอกเฉพาะ `SKILL.md` เพราะต้องมี `references/` ด้วย

## การใช้งาน

เรียกใช้ Skill ใน Codex ด้วย `$webman-development` หรือใน Claude Code ด้วย `/webman-development` เอเจนต์อาจโหลด Skill นี้โดยอัตโนมัติเมื่อพบงาน Webman
