<?php

use \DateTime;

class LoanCalculator
{
    public function __construct()
    {
        
    }
    public function GetLoanViewData($loanparameters)
    {
        $listloan = array();
        $TotalNumberofPayments = $loanparameters["NoOfPaymentYears"] * $loanparameters["NoOfYearlyInstallmentalPayments"];
        $scheduledpayment = $this->GetScheduledPayment($TotalNumberofPayments, $loanparameters["LoanPrincipal"], $loanparameters["InterestRate"]);
        $lastpaymenttime = new DateTime($loanparameters["PaymentStartDate"]);
       // echo $lastpaymenttime->format('Y-m-d');
        //exit;
        $startingbalance = $loanparameters["LoanPrincipal"];
        $interestRate=$loanparameters["InterestRate"];
        for ($i = 1; $i <= $TotalNumberofPayments; $i++)
        {
            $payment = $this->GetPayment($startingbalance, $interestRate, $scheduledpayment);
            $payment["PaymentNo"] = $i;
            $payment["ScheduledPayment"] = round($scheduledpayment,2);
           // $cummulativeInterest += Convert.ToDouble(payment.InterestAmount);
            // $payment["CummulativeInterestAmount"] = cummulativeInterest.ToString("n2");
            $payment["CummulativeInterestAmount"] =   round($payment["InterestAmount"],2);
            $interval = new DateInterval('P1M');
            $newdate=new DateTime($lastpaymenttime->format('Y-m-d'));
           // echo $newdate->format('Y-m-d');
            //$lastpaymenttime->add(new DateInterval('P1M'));
            $newdate->add($interval); // echo $lastpaymenttime->format('Y-m-d');
        //exit;
            $payment["paymentDate"] = $newdate;
            // $payment["paymentDate"] = $lastpaymenttime.AddMonths(1).ToShortDateString();
            $startingbalance = round($payment["EndingBalance"],2);
            // $lastpaymenttime = round($payment["paymentDate"]->format('Y-m-d'),2);
            $lastpaymenttime = $payment["paymentDate"];
            $listloan[$i]=$payment;

        }

        return $listloan;
    }
    private function GetPayment($startingbalance, $InterestRate, $scheduledpayment)
    {
        $payment = array();
        // payment.ScheduledPayment = scheduledpayment;
        $payment["BeginningBalance"] = round($startingbalance,2);

        $firstmul = 100 * 12;
        $monthlyInterest = $startingbalance * $InterestRate / $firstmul;
        $_interest = round($monthlyInterest, 2);
        $_principal = round(($scheduledpayment - $monthlyInterest), 2);
        $_ending = round($startingbalance - $_principal, 2);
        $payment["InterestAmount"] = round($_interest,2);
        $payment["PrincipalAmount"] = round($_principal,2);
        $payment["EndingBalance"] = round($_ending,2);
        return $payment;
    }

    private function GetScheduledPayment($totalNumberofPayments, $loanPrincipal, $interestRate)
    {
        $firstmul = 100 * 12;
        $intRate = $interestRate / $firstmul;
        $monthly = ($loanPrincipal * (pow((1 + $intRate), $totalNumberofPayments)) 
        * $intRate / (pow((1 + $intRate), $totalNumberofPayments) - 1));
        $monthly = round($monthly, 2);
        return $monthly;
    }
}

$calculator=new LoanCalculator();
$loandata=array(
    "InterestRate" => 12.4,
    "LoanPrincipal" => 10000,
    "NoOfPaymentYears" => 1,
    "PaymentStartDate"=>"20181010",
    "NoOfYearlyInstallmentalPayments" => 3
);
$payments=$calculator->GetLoanViewData($loandata);

//var_dump($payments);

foreach($payments as $payment)
{
    foreach($payment as $key=>$value)
    {
        if($key=="paymentDate")
        {
            echo $key ." = ". $value->format('Y-m-d')."<br/>";
        }
        else
        {
            echo $key ." = ". $value."<br/>";
        }   
    }
    echo "<hr/>";

}