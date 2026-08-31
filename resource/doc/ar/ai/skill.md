# Skills

يوفّر Webman Development Skill إرشادات Webman لـ Codex وClaude Code ووكلاء البرمجة المتوافقين مع Agent Skills. للحصول على الشرح الكامل، راجع [دليل Skills بالإنجليزية](https://webman.workerman.net/doc/en/ai/skill.html).

## التثبيت

اختر طريقة واحدة ونفّذ أوامر التثبيت الخاصة بالمشروع من مجلد مشروع Webman.

### 1. اطلب من وكيل البرمجة التثبيت (موصى به)

اطلب من Codex أو Claude Code: تثبيت `webman-development` من `https://github.com/webman-php/skills` للمشروع الحالي، مع الاحتفاظ بالمجلد الكامل والتحقق من اكتشاف `SKILL.md`.

### 2. Composer

```bash
composer require --dev "webman/skills:~1.0"
php vendor/bin/webman-skills install --agents=codex,claude-code
```

### 3. npx

```bash
npx skills add webman-php/skills --skill webman-development -a codex -a claude-code
```

### 4. تثبيت يدوي

```bash
git clone --depth 1 https://github.com/webman-php/skills.git webman-skills
```

انسخ مجلد `webman-skills/skills/webman-development/` كاملاً إلى `.agents/skills/webman-development/` لـ Codex أو `.claude/skills/webman-development/` لـ Claude Code. لا تنسخ `SKILL.md` وحده؛ مجلد `references/` جزء من Skill.

## الاستخدام

استدعِ Skill صراحة في Codex باستخدام `$webman-development`، أو في Claude Code باستخدام `/webman-development`. يمكن للوكلاء أيضاً تحميله تلقائياً عند اكتشاف مهمة Webman.
