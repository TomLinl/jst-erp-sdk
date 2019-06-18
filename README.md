### jst-erp-sdk
对接聚水潭 ERP 开放接口的 PHP SDK
## 安装方法
```shell
composer require jst/jst-erp-sdk
```
## 使用方法
店铺查询的接口
```php
use Jst\RpcClient;

$env = [
    'sandbox' => true, //测试环境还是正式环境
    'debug_mode' => false, //是否输出日志
    'partner_id' => 'ywv5jGT8ge6Pvlq3FZSPol345asd',
    'partner_key' => 'ywv5jGT8ge6Pvlq3FZSPol2323',
    'token' => '181ee8952a88f5a57db52587472c3798'
];

$erp = new RpcClient($env['sandbox'], $env['partner_id'], $env['partner_key'], $env['token']);

//普通接口调用方式,查询全部店铺信息
$response = $erp->call('shops.query', ['nicks' => ['微合伙人1', '爱牙帮']]);
var_dump($response);
```
