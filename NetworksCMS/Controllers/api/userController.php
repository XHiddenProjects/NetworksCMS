<?php
namespace NetWorks\api;
use NetWorks\Model\UserModel;
use Error;
class UserController extends Controller{
    public function listAction(): never{
        $strErrorDesc = '';
        $requestMethod = $_SERVER["REQUEST_METHOD"];
        $arrQueryStringParams = $this->getQueryStringParams();
        switch (strtoupper(string: $requestMethod)) {
            case 'GET':
                try {
                    $userModel = new UserModel();
                    $cond = '';
                    if (isset($arrQueryStringParams['limit']) && $arrQueryStringParams['limit']) {
                        $cond .= "LIMIT {$arrQueryStringParams['limit']}";
                    }
                    if(isset($arrQueryStringParams['joined'])){
                        $cond.='WHERE ';
                        if (isset($arrQueryStringParams['joined']) && $arrQueryStringParams['joined']) {
                            $cond .= "strftime('%Y',joined) BETWEEN ".implode(separator: ' AND ',array: array_map(callback: function($e): string{return "\"$e\"";},array: explode(separator: '-',string: $arrQueryStringParams['joined'])))."";
                        }
                    }
                    $arrUsers = $userModel->getUsers(conditions: $cond);
                    $responseData = json_encode(value: $arrUsers);
                } catch (Error $e) {
                    $strErrorDesc = $e->getMessage().'Something went wrong! Please contact support.';
                    $strErrorHeader = 'HTTP/1.1 500 Internal Server Error';
                }
            break;
            default:
                $strErrorDesc = 'Method not supported';
                $strErrorHeader = 'HTTP/1.1 422 Unprocessable Entity';
            break;
        }
        // send output 
        if (!$strErrorDesc) {
            $this->sendOutput(
                data: $responseData,
                httpHeaders: ['Content-Type: application/json', 'HTTP/1.1 200 OK']
            );
        } else {
            $this->sendOutput(data: json_encode(value: ['error' => $strErrorDesc]), 
                httpHeaders: ['Content-Type: application/json', $strErrorHeader]
            );
        }
    }
}
?>