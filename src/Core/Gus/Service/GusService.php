<?php

namespace Lea\Module\Gus\Service;

use Exception;
use GusApi\GusApi;
use GusApi\ReportTypes;
use Lea\Core\Service\ServiceInterface;

final class GusService implements ServiceInterface
{
    function __construct()
    {
        $this->gus = new GusApi('af77b7b8657c41fab6c3');
        // $gus = new GusApi('abcde12345abcde12345'); //dev
        $this->gus->login();
    }

    /**
     * Formatowanie danych NIP / REGON dla frontu
     */
    private function formatOutput(array $data): array
    {
        $headquarter = [
            'main' => "1",
            'city' => $data['city'],
            'post_code' => $data['post_code'],
            'address' => $data['address'],
            'correspondence' => "1",
        ];

        $res['company_name'] = $data['company_name'];
        $res['regon'] = $data['regon'];
        $res['nip'] = $data['nip'];
        $res['krs'] = $data['krs'] ?? null;
        $res['company_creation_date'] = $data['company_creation_date'] ?? null;
        $res['headquarters'][] = $headquarter;

        return $res;
    }

    public function returnData($type, $val)
    {
        $type = strtolower($type);
        switch ($type) {
            case "nip":
                $gusReports = $this->gus->getByNip($val);
                $res = $this->getData($gusReports);
                $res['nip'] = $val;
                $res = $this->formatOutput($res);

                return $res;
                break;
            case "regon":
                $gusReports = $this->gus->getByRegon($val);
                $res = $this->getData($gusReports);
                $res = $this->formatOutput($res);

                return $res;
                break;
            case "krs":
                $gusReports = $this->gus->getByKrs($val);
                return $this->getData($gusReports);
                break;
            case "ceidg":
                $this->getCEIDG();
                break;
            case "teryt":
                $this->getTeryt();
                break;
            default:
                break;
        }
    }

    /** pobranie z teryt */
    private function getTeryt()
    {
        $wsdl = 'https://uslugaterytws1.stat.gov.pl/wsdl/terytws1.wsdl';
        include('./api/webservices.php');
        $webservice = new TERYT_Webservices('TestPubliczny', '1234abcd', 'test', true);
        try {
            $towns = $webservice->town_search('Gdańsk');
            var_dump($towns);
        } catch (SoapFault $exception) {
            var_dump($exception);
        }
    }
    /** pobranie danych z ceidg */
    private function getCEIDG()
    {
        $url = 'https://datastoretest.ceidg.gov.pl/CEIDG.DataStore/services/DataStoreProvider201901.svc';
        $client = new SoapClient($url);
        resp($client);
    }
    /** pobranie pelnych informacji */
    private function getData($gusReports)
    {
        foreach ($gusReports as $gusReport) {
            $ret_arr = array(
                'company_name' => $gusReport->getName(),
                'nip' => $gusReport->getNip(),
                'activityEndDate' => $gusReport->getActivityEndDate(),
                'regon' => $gusReport->getRegon(),
                'province' => $gusReport->getProvince(),
                'address' => $gusReport->getStreet() . ' ' . $gusReport->getPropertyNumber() . (strlen($gusReport->getApartmentNumber()) > 0 ? "/" . $gusReport->getApartmentNumber() : ""),
                'post_code' => $gusReport->getZipCode(),
                'city' => $gusReport->getCity()
            );
            if ($gusReport->getType() == 'p') { //7381099191
                $reportType = ReportTypes::REPORT_PUBLIC_LAW;
                $fullReport = $this->gus->getFullReport($gusReport, $reportType);
                $ret_arr["krs"] = $fullReport[0]["praw_numerWRejestrzeEwidencji"];
                $ret_arr["company_creation_date"] = $fullReport[0]["praw_dataRozpoczeciaDzialalnosci"];
            }
        }
        return $ret_arr;
    }
}
