## स्टॉम्प कतार

स्टॉम्प सरल (धारा) पाठ प्रेषण समझौता है, जो एक संदेश अग्रेषित करने के लिए एक संदेशगार (स्टम्प मीडिएटर) के साथ संवाद करने के लिए एक समर्थ आपसी जोड़ प्रदान करता है। [वर्करमैन/स्टम्प](https://github.com/walkor/stomp) ने स्टॉम्प ग्राहक को अंगीकृत किया है, जो रैबिटएमक्यू, अपोलो, ऐक्टिवएमक्यू आदि संदेश कतार परिदृश्यों के लिए प्रमुख रूप से उपयोग किया जाता है।

## स्थापना
`कंपोजर पद्धति: वेबमैन/स्टम्प` 

## विन्यास
विन्यास फ़ाइल `कॉन्फ़िग/प्लगइन/वेबमैन/स्टम्प` नीचे है

## संदेश पोस्ट करें
```php
<?php
namespace app\controller;

use support\Request;
use Webman\Stomp\Client;

class Index
{
    public function queue(Request $request)
    {
        // कतार
        $queue = 'उदाहरण';
        // डेटा (एरे को पेशेवर करने के लिए स्वयं अनुक्रमणिकरण की आवश्यकता है, जैसे कि json_encode, serialize आदि)
        $data = json_encode(['to' => 'tom@gmail.com', 'content' => 'hello']);
        // कार्य प्रारंभ करें
        Client::send($queue, $data);

        return response('रेडिस कतार परीक्षण');
    }

}
```
> अन्य परियोजनाओं के साथ समर्थ होने के लिए, स्टॉम्प घटक स्वचालित रूप से क्रमवारीकरण और क्रमवारीकरण की सुविधा प्रदान नहीं करती है, यदि डेटा कतार की जाती है, तो स्वयं को क्रमवारीकरण करना चाहिए, सेवन करने का समय स्वयं को क्रमवारीकरण करना है

## संदेश सेव करना
नया सिर्जन करें `app/queue/stomp/MyMailSend.php` (कोई भी क्लास नाम, psr4 मानकों के अनुसार सही)।
```php
<?php
namespace app\queue\stomp;

use Workerman\Stomp\AckResolver;
use Webman\Stomp\Consumer;

class MyMailSend implements Consumer
{
    // कतार का नाम
    public $queue = 'उदाहरण';

    // कनेक्शन नाम, stomp.php में कनेक्शन को संदर्भित करता है
    public $connection = 'डिफ़ॉल्ट';

    // जब AckResolver को कहने की आवश्यकता होती है कि आस्वादन सफलतापूर्वक हुआ है
    // जब auto होता है, तो $ack_resolver->ack() को कहने की आवश्यकता नहीं होती है
    public $ack = 'auto';

    // खप्पा
    public function consume($data, AckResolver $ack_resolver = null)
    {
        // अगर डेटा एरे है, तो स्वयं को क्रमवारीकरण करना होगा
        var_export(json_decode($data, true)); // आउटपुट ['to' => 'tom@gmail.com', 'content' => 'hello']
        // सर्वर को बताएं कि, सफलतापूर्वक सेव किया गया है
        $ack_resolver->ack(); // जब ack auto होता है, तो इस कॉल को छोड़ा जा सकता है
    }
}
```

# स्टॉम्प परिदृश्य को खोलें
रैबिटएमक्यू डिफ़ॉल्ट रूप से स्टॉम्प समझौता नहीं खोलता है, निम्नलिखित कमांड का पालन करके इसे खोलना होगा
```shell
rabbitmq-plugins enable rabbitmq_stomp
```
खोलने के बाद स्टम्प का द्वार 61613 पर डिफ़ॉल्ट रूप से होता है।
