# মৌলিক প্লাগইন

মৌলিক প্লাগইনগুলি সাধারণভাবে সাধারণ কোডগুলি হয়, যেমন composer দিয়ে ইনস্টল করা হয়, এবং কোডগুলি vendor ডিরেক্টরিতে থাকে। প্লাগইন ইনস্টল করার সময় কোনো নিজস্ব কনফিগারেশন (মিডলওয়্যার, প্রসেস, রাউট ইত্যাদি কনফিগারেশন) কে `{মূল প্রজেক্ট} config/plugin` ডিরেক্টরিতে স্বয়ংক্ষেত্রে কপি করা যেতে পারে, এবং webman প্রধান কনফিগারেশনে এই কনফিগারেশনগুলি স্বয়ংক্ষেত্রে চলে আসা হবে, যা প্লাগইনগুলির কোনও জীবনচক্রায় webman নিয়ে আসতে পারে।

বিস্তারিত দেখুন [মৌলিক প্লাগইন তৈরি](create.md)
