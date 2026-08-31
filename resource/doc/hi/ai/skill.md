# Skills

Webman Development Skill, Codex, Claude Code और Agent Skills-संगत कोडिंग एजेंटों को Webman मार्गदर्शन देता है। पूरी जानकारी के लिए [अंग्रेज़ी Skills गाइड](https://webman.workerman.net/doc/en/ai/skill.html) देखें।

## स्थापना

एक तरीका चुनें और Webman प्रोजेक्ट की मूल निर्देशिका से प्रोजेक्ट कमांड चलाएँ।

### 1. कोडिंग एजेंट से इंस्टॉल करवाएँ (अनुशंसित)

Codex या Claude Code से वर्तमान प्रोजेक्ट के लिए `https://github.com/webman-php/skills` से `webman-development` इंस्टॉल करने, पूरा फ़ोल्डर रखने और `SKILL.md` की खोज की पुष्टि करने को कहें।

### 2. Composer

```bash
composer require --dev webman/skills
php vendor/bin/webman-skills install --agents=codex,claude-code
```

### 3. npx

```bash
npx skills add webman-php/skills --skill webman-development -a codex -a claude-code
```

### 4. मैन्युअल स्थापना

```bash
git clone --depth 1 https://github.com/webman-php/skills.git webman-skills
```

`webman-skills/skills/webman-development/` को Codex के लिए `.agents/skills/webman-development/` या Claude Code के लिए `.claude/skills/webman-development/` में पूरा कॉपी करें। केवल `SKILL.md` कॉपी न करें; `references/` भी आवश्यक है।

## उपयोग

Codex में `$webman-development` या Claude Code में `/webman-development` से Skill चलाएँ। Webman कार्य पहचानने पर एजेंट इसे अपने-आप भी लोड कर सकता है।
