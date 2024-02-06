# எஞ்சின்க் புதியைப் பிரதிபார்க்க
webman வெளியேறுவதற்கு நேரமாக ஒரு எஞ்சின்க் புதியையோடு வடிதம் சேர்க்கவும் ஒருமுறை அது கொண்டவைகளைக் கொண்டுள்ளது.

- உருயரசை nginx-உகள் மூலம் செயல்பாடு வழங்கப்படும், webman வணிக புரி மன்றத்தில் கவணம் செலுதல்
- 80, 443 துருக்களை பல மூலம் ஒரு கொழுப்பு மண்டலங்களை பயன்படுத்தி, குறிப்பிட்ட தளங்களை வேதனப்படுத்த, ஒரு சர்வர் வேண்டும்
- php-fpm மற்றும் webman அமைப்பு ஒன்றுபடி இருவர் வையும் உள்ளன
- எஞ்சின்க் புரி ssl ஐ உணர்வுபடுத்த மூலம் https ஐ நுழைய, அதிக எளிமையாக அதிக செயல்படுத்து
- வெளியேறுவது போன்ற எச்சரிக்கான வெளியேறுவது போன்றவைகளை வரையாக ஒழிக்க.

## எஞ்சின்க் புதிய உதாரணம்
```
upstream webman {
    server 127.0.0.1:8787;
    keepalive 10240;
}

server {
  server_name தளபதிகுறள்;
  listen 80;
  access_log off;
  root /your/webman/public;

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

பொதுவாக மேம்பட்ட ஒரு அமைப்பை உள்ளாள், உரையாடலில் server_name மற்றும் ரூட் அமைப்பை உண்டாக்கும் மட்டுமே தேவை, பிற புல்லாட்சி தேவை இல்லை.

