<?php

namespace Lea\Module\Gus\Service;

use GusApi\GusApi;
use GusApi\ReportTypes;
use Lea\Core\Service\ServiceInterface;

final class GusService implements ServiceInterface
{
    function __construct()
    {
        $this->gus = new GusApi($_ENV['GUS_API_KEY']);
        $this->gus->login();
    }

    public function returnData($type, $val)
    {
        $type = strtolower($type);
        switch ($type) {
            case "nip":
                $gusReports = $this->gus->getByNip($val);
                $res = $this->getData($gusReports);
                $res['nip'] = (string)$val;
                // $res = $this->formatOutput($res);

                return $res;
                break;
            default:
                break;
        }
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
