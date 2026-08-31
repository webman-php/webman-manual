# Skills

Webman Development Skill Codex, Claude Code এবং Agent Skills-সমর্থিত কোডিং এজেন্টকে Webman-এর নির্দেশনা দেয়। বিস্তারিত ব্যবহারের জন্য [ইংরেজি Skills গাইড](https://webman.workerman.net/doc/en/ai/skill.html) দেখুন।

## ইনস্টলেশন

একটি পদ্ধতি বেছে নিন এবং Webman প্রকল্পের মূল ডিরেক্টরি থেকে কমান্ড চালান।

### ১. কোডিং এজেন্টকে ইনস্টল করতে বলুন (প্রস্তাবিত)

Codex বা Claude Code-কে বলুন: বর্তমান প্রকল্পে `https://github.com/webman-php/skills` থেকে `webman-development` ইনস্টল করতে, সম্পূর্ণ ডিরেক্টরি রাখতে এবং `SKILL.md` খুঁজে পাওয়া যাচ্ছে কি না যাচাই করতে।

### ২. Composer

```bash
composer require --dev webman/skills
php vendor/bin/webman-skills install --agents=codex,claude-code
```

### ৩. npx

```bash
npx skills add webman-php/skills --skill webman-development -a codex -a claude-code
```

### ৪. ম্যানুয়াল ইনস্টলেশন

```bash
git clone --depth 1 https://github.com/webman-php/skills.git webman-skills
```

`webman-skills/skills/webman-development/` সম্পূর্ণ ডিরেক্টরি Codex-এর জন্য `.agents/skills/webman-development/` অথবা Claude Code-এর জন্য `.claude/skills/webman-development/`-এ কপি করুন। শুধু `SKILL.md` কপি করবেন না; `references/`-ও প্রয়োজন।

## ব্যবহার

Codex-এ `$webman-development` এবং Claude Code-এ `/webman-development` লিখে Skill সরাসরি চালু করুন। Webman কাজ শনাক্ত হলে এজেন্ট এটি স্বয়ংক্রিয়ভাবেও ব্যবহার করতে পারে।
