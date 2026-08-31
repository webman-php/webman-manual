# Skills

Webman Development Skill, Codex, Claude Code ve Agent Skills uyumlu diğer kodlama ajanlarına Webman rehberliği sağlar. Ayrıntılar için [İngilizce Skills kılavuzuna](https://webman.workerman.net/doc/en/ai/skill.html) bakın.

## Kurulum

Bir yöntem seçin ve proje komutlarını Webman projesinin kök dizininde çalıştırın.

### 1. Kodlama ajanından kurmasını isteyin (önerilir)

Codex veya Claude Code'dan mevcut proje için `https://github.com/webman-php/skills` adresindeki `webman-development` Skill'ini kurmasını, tam dizini korumasını ve `SKILL.md` dosyasının keşfedildiğini doğrulamasını isteyin.

### 2. Composer

```bash
composer require --dev webman/skills
php vendor/bin/webman-skills install --agents=codex,claude-code
```

### 3. npx

```bash
npx skills add webman-php/skills --skill webman-development -a codex -a claude-code
```

### 4. Manuel kurulum

```bash
git clone --depth 1 https://github.com/webman-php/skills.git webman-skills
```

`webman-skills/skills/webman-development/` dizininin tamamını Codex için `.agents/skills/webman-development/` veya Claude Code için `.claude/skills/webman-development/` konumuna kopyalayın. Yalnızca `SKILL.md` dosyasını kopyalamayın; `references/` da gereklidir.

## Kullanım

Codex'te `$webman-development`, Claude Code'da `/webman-development` ile Skill'i açıkça çağırın. Webman görevlerinde ajan Skill'i otomatik olarak da yükleyebilir.
