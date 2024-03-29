# एनजिन्क्स प्रॉक्सी
जब webman को सीधे बाहरी नेटवर्क एक्सेस प्रदान करने की आवश्यकता होती है, तो webman के आगे एक एनजिन्स प्रॉक्सी जोड़ना सुझाया जाता है, जिसके अनुसार निम्नलिखित फायदे होते हैं।

- एनजिन्स द्वारा स्थिर संसाधनों का प्रक्रियण, जिससे webman का ध्यान केवल व्यावसायिक तरीके से हो
- कई webman को 80, 443 पोर्ट साझा करने दें, डोमेन के माध्यम से विभिन्न स्थानों को अंतर्निर्देशित करें, एकल सर्वर पर कई स्थानों का उपयोग करना संभव करें
- php-fpm और webman डिज़ाइन को समृद्ध करना संभव हो
- एनजिन्स द्वारा ssl का अनुरोध करके https प्राप्त करना, जो अधिक सरल और दक्ष होता है
- बाहरी नेटवर्क के कुछ अवैध अनुरोधों को सख्तता से फ़िल्टर कर सकता है

## एनजिनक्स प्रॉक्सी उदाहरण
```
upstream webman {
    server 127.0.0.1:8787;
    keepalive 10240;
}

server {
  server_name वेबसाइट_डोमेन;
  listen 80;
  access_log off;
  root /तुम्हारा/webman/सार्वजनिक;

  location ^~ / {
      proxy_set_header X-Real-IP $remote_addr;
      proxy_set_header Host $http_host;
      proxy_set_header X-Forwarded-Proto $scheme;
      proxy_http_version 1.1;
      proxy_set_header Connection "";
      if (!-f $request_filename){
          proxy_pass http://webman;
      }
  }
}
```

सामान्यतः उपर्युक्त कॉन्फ़िगरेशन में, डेवलपर को केवल server_name और root को वास्तविक मानों पर कॉन्फ़िगर करना होता है, अन्य फ़ील्ड को कॉन्फ़िगर नहीं करना होता है।
