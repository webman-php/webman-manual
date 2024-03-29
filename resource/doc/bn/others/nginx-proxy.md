# এনজিনক্স প্রক্সি
যখন webman বাইরের এক্সেস সরবরাহ করার প্রয়োজন হয়, তখন প্রস্তাবিত হয় যে webman একটি nginx প্রক্সি যোগ করা উচিত, যা দ্বারা নিম্নলিখিত সুবিধা প্রাপ্ত করা যায়।

- স্ট্যাটিক রিসোর্স এনজিনক্স দ্বারা প্রসেস করা হয়, যা webman-কে ব্যবসা লজিক প্রসেসিং-এ মনোবন্ধ করে
- ৮০, ৪৪৩ পোর্ট অনুমতি দিয়ে একাধিক webman সংযোগ করা, ডোমেইন দ্বারা ভিন্ন ওয়েবসাইট পরিচালনা করা, একটি সার্ভারে একাধিক ওয়েবসাইট ডিপ্লয় করা
- php-fpm এবং webman কো-অস্তিত্ব সম্পন্ন করতে পারে
- এনজিনক্স প্রক্সি এসএসএল এস মাধ্যমে এসএইচটিপিএস পূর্ণতা করতে পারে, এটি আরও সহজ এবং দক্ষ
- বাহ্যিক অবৈধ অনুরোধ সম্পর্কে ক্ঠিন ফিল্টার করা যায়।

## এনজিনক্স প্রক্সি উদাহরণ
```nginx
upstream webman {
    server 127.0.0.1:8787;
    keepalive 10240;
}

server {
  server_name ওয়েবসাইট_নাম;
  listen ৮০;
  access_log off;
  root /আপনার/webman/পাবলিক;

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

সাধারণভাবে উপরের কনফিগারেশনে ডেভেলপার একটি ডোমেইন_নাম এবং রুট কনফিগার করতে হবে, অন্যান্য ফিল্ড কনফিগারেশন করা আবশ্যক হবে না।
