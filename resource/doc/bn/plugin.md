# প্লাগইন
প্লাগইনগুলি ভাগ করা হয় **বেসিক প্লাগইন** এবং **অ্যাপ্লিকেশন প্লাগইন** এ।

#### বেসিক প্লাগইন
বেসিক প্লাগইনগুলি হলো webman এর মৌলিক কোম্পোনেন্ট, যা একটি সাধারণ ক্লাস লাইব্রেরি হতে পারে (যেমন: webman/think-orm), একটি সাধারণ মিডলওয়ে (যেমন: webman/cors), বা একটি রাউট কনফিগারেশন সেট (যেমন: webman/auto-route), অথবা একটি কাস্টম প্রসেস (যেমন: webman/redis-queue) এবং অন্যান্য।

আরও দেখুন [বেসিক প্লাগইন](plugin/base.md)

> **লক্ষণীয়**
> বেসিক প্লাগইনের জন্য webman>=1.2.0 প্রয়োজন

#### অ্যাপ্লিকেশন প্লাগইন
অ্যাপ্লিকেশন প্লাগইন হলো একটি সম্পূর্ণ অ্যাপ্লিকেশন, উদাহরণস্বরূপ প্রশ্নোত্তর সিস্টেম, সিএমএস সিস্টেম, মলল সিস্টেম ইত্যাদি।
আরও দেখুন [অ্যাপ্লিকেশন প্লাগইন](app/app.md)

> **অ্যাপ্লিকেশন প্লাগইন**
> অ্যাপ্লিকেশন প্লাগইনের জন্য webman>=1.4.0 প্রয়োজন
