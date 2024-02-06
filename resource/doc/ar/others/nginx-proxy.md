# وكالة Nginx
عندما يحتاج webman إلى توفير الوصول المباشر إلى الإنترنت ، من المستحسن إضافة وكالة Nginx قبل webman ، وهذا يأتي مع الفوائد التالية.

- يتم معالجة الموارد الثابتة بواسطة Nginx ، مما يسمح لـ webman بالتركيز على معالجة منطق الأعمال
- السماح لعدة webman باستخدام منفذ 80 و 443 من خلال تمييز النطاق المختلف ، مما يتيح نشر موقع واحد على خادم واحد
- يمكن تحقيق تواجد php-fpm إلى جانب هيكل webman
- Nginx وكالة تنفيذ SSL لتحقيق https ، مما يجعلها أكثر بساطة وكفاءة
- يمكن تصفية بعض الطلبات غير القانونية من الإنترنت بصورة صارمة

## مثال وكالة Nginx
```
upstream webman {
    server 127.0.0.1:8787;
    keepalive 10240;
}

server {
  server_name اسم النطاق;
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

عمومًا ، فإن المطورين يحتاجون فقط إلى تكوين server_name و root كقيم فعلية ، ولا يحتاجون لتكوين الحقول الأخرى.
