<?php

/**
 * Class Rekvizitai
 */

/*
-- Test data for searching by name
<?xml version="1.0" encoding="utf-8"?>
<data>
    <status>success</status>
    <companies>
        <company>
            <code>302247835</code>
            <title>UAB &quot;Roxlogic IT Solutions&quot;</title>
            </company>
            <company>
            <code>302656401</code>
            <title>UAB &quot;Roxus&quot;</title>
            </company>
            <company>
            <code>302607379</code>
            <title>ROXI BIURAS, UAB</title>
            </company>
            <company>
            <code>126411091</code>
            <title>Roxy Cargologistik, UAB</title>
            </company>
            <company>
            <code>301707654</code>
            <title>Roxeda, UAB</title>
            </company>
            <company>
            <code>302862230</code>
            <title>UAB &quot;Rox Capital&quot;</title>
        </company>
    </companies>
</data>

-- Test data for searching by number
<?xml version="1.0" encoding="utf-8"?>
<data>
    <status>success</status>
    <companies>
        <company>
            <code>302656401</code>
            <title>UAB &quot;Roxus&quot;</title>
            <address>Parko g. 11, Plaučiškiai, LT-83254 Pakruojo r.</address>
            <city>Pakruojo r.</city>
            <street>Parko g.</street>
            <houseNo>11</houseNo>
            <addressRest>Plaučiškiai</addressRest>
            <postCode>LT-83254</postCode>
            <categories>Maisto gamyba; Maisto produktai; Mėsa, mėsos produktai</categories>
            <phone></phone>
            <mobile>+370 685 67510</mobile>
            <fax></fax>
            <website></website>
            <email>uabroxus@gmail.com</email>
            <pvmCode>LT100006341012</pvmCode>
            <url>http://www.rekvizitai.lt/imone/roxus/</url>
        </company>
    </companies>
</data>

 */
class Rekvizitai
{
    public $CI;

    private $baseUrl = 'http://www.rekvizitai.lt/api-xml/?apiKey={apiKey}&clientId=1';
    private $titleUrl = '&method=search&query={title}';
    private $numberUrl = '&method=companyDetails&code={number}';
    private $apiKey = '721b373c2e9a045a917a232819f0ca36';

    /**
     * Rekvizitai constructor.
     */
    public function __construct()
    {
        $this->CI =& get_instance();
        $this->CI->load->model('Mdl_api_search');
    }

    /**
     * Get url
     * @param $title
     * @param $number
     * @return string
     */
    private function getUrl($title, $number)
    {
        if ($title != null) {
            $url =
                str_replace('{apiKey}', $this->apiKey, $this->baseUrl) .
                str_replace('{title}', $title, $this->titleUrl);
        } else {
            $url =
                str_replace('{apiKey}', $this->apiKey, $this->baseUrl) .
                str_replace('{number}', $number, $this->numberUrl);
        }

        return $url;
    }

    /**
     * Search
     * @param $clientId
     * @param $title
     * @param $number
     * @return array
     * @throws Exception
     */
    public function search($clientId, $title, $number)
    {
        if ($title == null && $number == null) {
            throw new Exception('Title or number should not be equal null');
        }

        if ($title) {
            $result = $this->CI->Mdl_api_search->search_by_title($title);
            if ($result) {
                return $this->parseResponse($result->apis_response_raw);
            }
        } elseif ($number) {
            $result = $this->CI->Mdl_api_search->search_by_number($number);
            if ($result) {
                return $this->parseResponse($result->apis_response_raw);
            }
        }

        $apiResponse = file_get_contents($this->getUrl($title, $number));
        $this->CI->Mdl_api_search->save(null, [
            'apis_date'         => (new DateTime())->format('Y-m-d H:i:s'),
            'apis_client_id'    => 3,
            'apis_req_title'    => $title,
            'apis_req_number'   => $number,
            'apis_res_title'    => '',
            'apis_res_number'   => '',
            'apis_response_raw' => $apiResponse
        ]);

        return $this->parseResponse($apiResponse);
    }

    /**
     * Parse response
     * @param $raw
     * @return array
     */
    public function parseResponse($raw)
    {
        $response = [
            'title'    => '',
            'number'   => '',
            'city'     => '',
            'street'   => '',
            'houseNo'  => '',
            'postCode' => '',
            'email'    => '',
            'phone'    => '',
            'mobile'   => '',
            'fax'      => '',
            'website'  => '',
            'pvmCode'  => ''
        ];

        $raw = simplexml_load_string($raw);
        if (!$raw) {
            return $response;
        }

        if ($raw->status != 'success') {
            return $response;
        }

        foreach ($raw->companies->company as $company) {
            $response['title']    = (string) $company->title;
            $response['number']   = (string) $company->code;
            $response['city']     = (string) $company->city;
            $response['street']   = (string) $company->street;
            $response['houseNo']  = (string) $company->houseNo;
            $response['postCode'] = (string) $company->postCode;
            $response['email']    = (string) $company->email;
            $response['phone']    = (string) $company->phone;
            $response['mobile']   = (string) $company->mobile;
            $response['fax']      = (string) $company->fax;
            $response['website']  = (string) $company->website;
            $response['pvmCode']  = (string) $company->pvmCode;

            $response['title'] = preg_replace("/^UAB\s/", "", $response['title']);
            $response['title'] = preg_replace("/^AB\s/", "", $response['title']);
            $response['title'] = trim($response['title']);
            $response['title'] = trim($response['title'], '"');
            $response['title'] = trim($response['title']);
        }

        return $response;
    }
}
