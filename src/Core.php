<?php
namespace CosmosdbClient;

class Core
{
    private $masterkey;
    private $account;

    public function __construct(string $masterkey,
                                string $account){
        $this->masterkey = $masterkey;
        $this->account = $account;
    }

    public function getAuthHeaders(string $verb,
                                   string $resourceType,
                                   string $resourceId)
    {
        $xMsDate = gmdate('D, d M Y H:i:s T', strtotime('+2 minutes'));
        $master = 'master';
        $token = '1.0';
        $xMsVersion = '2017-02-22';
        $key = base64_decode($this->masterkey);
        $stringToSign = $verb . "\n" .
                        $resourceType . "\n" .
                        $resourceId . "\n" .
                        $xMsDate . "\n" .
                        "\n";
        $sig = base64_encode(hash_hmac('sha256',
                                       strtolower($stringToSign),
                                       $key,
                                       true));
        return array(
            'Content-Type' => 'application/json',
            'x-ms-date' => $xMsDate,
            'x-ms-version'=> $xMsVersion,
            'Authorization' => urlencode("type=$master&ver=$token&sig=$sig"),
            # 'Cache-Control' => 'no-cache'
        );
    }

    public function makeBaseUri(){
        return 'https://' . $this->account . '.documents.azure.com';
    }
}