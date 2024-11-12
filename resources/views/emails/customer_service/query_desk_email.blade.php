<!DOCTYPE html>
<html lang="en">

<head>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Query Desk</title>
</head>

<body>

   <table cellpadding="0" cellspacing="0" width="700" align="center"
      style="border:solid 1px #ccc;font-family:'calibri','Segoe UI','Helvetica Neue',Helvetica,Arial,sans-serif">
      <tbody>
         <tr>
            <td>
               <table cellpadding="0" cellspacing="0" style="width:100%;border-bottom:1px solid #ddd">
                  <tbody>
                     <tr>
                        <td style="width:2%"></td>
                        <td style="width:96%">
                           <table border="0" cellpadding="0" cellspacing="0" style="width:100%">
                              <tbody>
                                 <tr>
                                    <td align="left" height="80">
                                       <img src="https://www.nuedgecorporate.com/images/logo.png" height="80"
                                          alt="NuedgeLogo" class="CToWUd" data-bit="iit">
                                    </td>
                                 </tr>
                              </tbody>
                           </table>
                        </td>
                        <td style="width:2%"></td>
                     </tr>
                  </tbody>
               </table>
               <table cellpadding="0" cellspacing="0" style="width:100%">
                  <tbody>
                     <tr>
                        <td style="width:2%"></td>
                        <td style="width:96%">
                           <table cellpadding="0" cellspacing="0" style="width:100%">
                              <tbody>
                                 <tr>
                                    <td height="10"></td>
                                 </tr>
                                 <tr>
                                    <td height="22"
                                       style="width:100%;text-align:left;color:#666666;vertical-align:top;font-weight:700;font-family:'calibri','Segoe UI','Helvetica Neue',Helvetica,Arial,sans-serif;font-size:14px">
                                       Dear {{$investor_name}},</td>
                                 </tr>
                                 <tr>
                                    <td height="22"
                                       style="width:100%;text-align:left;color:#666666;vertical-align:top;font-weight:400;font-size:14px;font-family:'calibri','Segoe UI','Helvetica Neue',Helvetica,Arial,sans-serif">
                                       Greetings from NuEdge Corporate Private Limited.</td>
                                 </tr>
                                 <tr>
                                    <td height="22"
                                       style="width:100%;text-align:left;color:#666666;vertical-align:top;font-weight:400;font-size:14px;font-family:'calibri','Segoe UI','Helvetica Neue',Helvetica,Arial,sans-serif">
                                       This is an auto-response email to tag your request with status
                                       <b>{{$query_status}}</b> Please do not reply to this email<br><br>Request you to
                                       kindly check reply description in below Status. If you find the reply irrelevant
                                       to you, you can reopen this query OR email us at<a
                                          href="mailto:service@nuedgecorporate.co.in" target="_blank">
                                          service@nuedgecorporate.co.in.</a>
                                    </td>
                                 </tr>
                              </tbody>
                           </table>
                        </td>
                        <td style="width:2%"></td>
                     </tr>
                  </tbody>
               </table>
               <table border="0" cellpadding="0 " cellspacing="0 " style="width:100%">
                  <tbody>
                     <tr>
                        <td height="20"></td>
                     </tr>
                     <tr>
                        <td style="width:1%"></td>
                        <td style="width:98%">
                           <table cellpadding="0 " cellspacing="0 " style="width:100%">
                              <tbody>
                                 <tr style="background:#f6f6f6">
                                    <td
                                       style="width:20%;font-weight:600;color:#000;font-size:14px;font-family:'calibri','Segoe UI','Helvetica Neue',Helvetica,Arial,sans-serif;padding:6px 0 6px 10px;border-right:1px solid #ddd;border-bottom:1px solid #ddd;border-left:1px solid #ddd;border-top:1px solid #ddd">
                                       Query ID
                                    </td>
                                    <td
                                       style="width:34%;color:#000;font-size:14px;text-align:left;font-family:'calibri','Segoe UI','Helvetica Neue',Helvetica,Arial,sans-serif;padding:6px 0 6px 10px;border-right:1px solid #ddd;border-bottom:1px solid #ddd;border-top:1px solid #ddd">
                                       {{$data->query_id}}</td>
                                    <td
                                       style="width:14%;font-weight:600;color:#000;font-size:14px;font-family:'calibri','Segoe UI','Helvetica Neue',Helvetica,Arial,sans-serif;padding:6px 0 6px 10px;border-right:1px solid #ddd;border-bottom:1px solid #ddd;border-top:1px solid #ddd">
                                       Query Status </td>
                                    <td
                                       style="width:30%;color:#000;font-size:14px;text-align:left;font-family:'calibri','Segoe UI','Helvetica Neue',Helvetica,Arial,sans-serif;padding:6px 0 6px 10px;border-right:1px solid #ddd;border-bottom:1px solid #ddd;border-top:1px solid #ddd">
                                       {{$data->status_name}}</td>
                                 </tr>
                                 <tr>
                                    <td
                                       style="width:20%;font-weight:600;color:#000;font-size:14px;font-family:'calibri','Segoe UI','Helvetica Neue',Helvetica,Arial,sans-serif;padding:6px 0 6px 10px;border-right:1px solid #ddd;border-bottom:1px solid #ddd;border-left:1px solid #ddd">
                                       Query type
                                    </td>
                                    <td
                                       style="width:34%;color:#000;font-size:14px;text-align:left;font-family:'calibri','Segoe UI','Helvetica Neue',Helvetica,Arial,sans-serif;padding:6px 0 6px 10px;border-right:1px solid #ddd;border-bottom:1px solid #ddd">
                                       {{$data->query_type}}</td>
                                    <td
                                       style="width:14%;font-weight:600;color:#000;font-size:14px;text-align:left;font-family:'calibri','Segoe UI','Helvetica Neue',Helvetica,Arial,sans-serif;padding:6px 0 6px 10px;border-right:1px solid #ddd;border-bottom:1px solid #ddd">
                                       Sub Type</td>
                                    <td
                                       style="width:30%;color:#000;font-size:14px;text-align:left;font-family:'calibri','Segoe UI','Helvetica Neue',Helvetica,Arial,sans-serif;padding:6px 0 6px 10px;border-bottom:1px solid #ddd;border-right:1px solid #ddd">
                                       {{$data->query_subtype}}</td>
                                 </tr>
                                 <tr style="background:#f6f6f6">
                                    <td
                                       style="width:20%;font-weight:600;color:#000;font-size:14px;font-family:'calibri','Segoe UI','Helvetica Neue',Helvetica,Arial,sans-serif;padding:6px 0 6px 10px;border-right:1px solid #ddd;border-bottom:1px solid #ddd;border-left:1px solid #ddd">
                                       Branch
                                    </td>
                                    <td
                                       style="width:34%;color:#000;font-size:14px;text-align:left;font-family:'calibri','Segoe UI','Helvetica Neue',Helvetica,Arial,sans-serif;padding:6px 0 6px 10px;border-right:1px solid #ddd;border-bottom:1px solid #ddd">
                                       Main Branch</td>
                                    <td
                                       style="width:20%;font-weight:600;color:#000;font-size:14px;text-align:left;font-family:'calibri','Segoe UI','Helvetica Neue',Helvetica,Arial,sans-serif;padding:6px 0 6px 10px;border-right:1px solid #ddd;border-bottom:1px solid #ddd">
                                       Query Received through </td>
                                    <td
                                       style="width:30%;color:#000;font-size:14px;text-align:left;font-family:'calibri','Segoe UI','Helvetica Neue',Helvetica,Arial,sans-serif;padding:6px 0 6px 10px;border-bottom:1px solid #ddd;border-right:1px solid #ddd">
                                       {{($data->query_receive_through)?$data->query_receive_through:"N/A"}}</td>
                                 </tr>
                                 <tr>
                                    <td
                                       style="width:20%;font-weight:600;color:#000;font-size:14px;font-family:'calibri','Segoe UI','Helvetica Neue',Helvetica,Arial,sans-serif;padding:6px 0 6px 10px;border-right:1px solid #ddd;border-bottom:1px solid #ddd;border-left:1px solid #ddd">
                                       Query Received By
                                    </td>
                                    <td
                                       style="width:34%;color:#000;font-size:14px;text-align:left;font-family:'calibri','Segoe UI','Helvetica Neue',Helvetica,Arial,sans-serif;padding:6px 0 6px 10px;border-right:1px solid #ddd;border-bottom:1px solid #ddd">
                                       {{$data->entry_name}}</td>
                                    <td
                                       style="width:14%;font-weight:600;color:#000;font-size:14px;text-align:left;font-family:'calibri','Segoe UI','Helvetica Neue',Helvetica,Arial,sans-serif;padding:6px 0 6px 10px;border-right:1px solid #ddd;border-bottom:1px solid #ddd">
                                       Query Solved By</td>
                                    <td
                                       style="width:30%;color:#000;font-size:14px;text-align:left;font-family:'calibri','Segoe UI','Helvetica Neue',Helvetica,Arial,sans-serif;padding:6px 0 6px 10px;border-bottom:1px solid #ddd;border-right:1px solid #ddd">
                                       N/A</td>
                                 </tr>
                                 <tr style="background:#f6f6f6">
                                    <td
                                       style="width:20%;font-weight:600;color:#000;font-size:14px;font-family:'calibri','Segoe UI','Helvetica Neue',Helvetica,Arial,sans-serif;padding:6px 0 6px 10px;border-right:1px solid #ddd;border-bottom:1px solid #ddd;border-left:1px solid #ddd">
                                       Business type
                                    </td>
                                    <td
                                       style="width:34%;color:#000;font-size:14px;text-align:left;font-family:'calibri','Segoe UI','Helvetica Neue',Helvetica,Arial,sans-serif;padding:6px 0 6px 10px;border-right:1px solid #ddd;border-bottom:1px solid #ddd">
                                       B2C-Direct</td>
                                    <td
                                       style="width:14%;font-weight:600;color:#000;font-size:14px;text-align:left;font-family:'calibri','Segoe UI','Helvetica Neue',Helvetica,Arial,sans-serif;padding:6px 0 6px 10px;border-right:1px solid #ddd;border-bottom:1px solid #ddd">
                                       Employee/Sub Broker</td>
                                    <td
                                       style="width:30%;color:#000;font-size:14px;text-align:left;font-family:'calibri','Segoe UI','Helvetica Neue',Helvetica,Arial,sans-serif;padding:6px 0 6px 10px;border-bottom:1px solid #ddd;border-right:1px solid #ddd">
                                       Tanmay Sarkar</td>
                                 </tr>
                                 <tr>
                                    <td
                                       style="width:20%;font-weight:600;color:#000;font-size:14px;font-family:'calibri','Segoe UI','Helvetica Neue',Helvetica,Arial,sans-serif;padding:6px 0 6px 10px;border-right:1px solid #ddd;border-bottom:1px solid #ddd;border-left:1px solid #ddd">
                                       Client Name
                                    </td>
                                    <td
                                       style="width:34%;color:#000;font-size:14px;text-align:left;font-family:'calibri','Segoe UI','Helvetica Neue',Helvetica,Arial,sans-serif;padding:6px 0 6px 10px;border-right:1px solid #ddd;border-bottom:1px solid #ddd">
                                       {{$data->investor_name}}</td>
                                    <td
                                       style="width:14%;font-weight:600;color:#000;font-size:14px;text-align:left;font-family:'calibri','Segoe UI','Helvetica Neue',Helvetica,Arial,sans-serif;padding:6px 0 6px 10px;border-right:1px solid #ddd;border-bottom:1px solid #ddd">
                                       PAN No</td>
                                    <td
                                       style="width:30%;color:#000;font-size:14px;text-align:left;font-family:'calibri','Segoe UI','Helvetica Neue',Helvetica,Arial,sans-serif;padding:6px 0 6px 10px;border-bottom:1px solid #ddd;border-right:1px solid #ddd">
                                       {{($data->investor_pan)?$data->investor_pan:"N/A"}}</td>
                                 </tr>
                                 <tr style="background:#f6f6f6">
                                    <td
                                       style="width:20%;font-weight:600;color:#000;font-size:14px;font-family:'calibri','Segoe UI','Helvetica Neue',Helvetica,Arial,sans-serif;padding:6px 0 6px 10px;border-right:1px solid #ddd;border-bottom:1px solid #ddd;border-left:1px solid #ddd">
                                       Application No
                                    </td>
                                    <td
                                       style="width:34%;color:#000;font-size:14px;text-align:left;font-family:'calibri','Segoe UI','Helvetica Neue',Helvetica,Arial,sans-serif;padding:6px 0 6px 10px;border-right:1px solid #ddd;border-bottom:1px solid #ddd">
                                       N/A</td>
                                    <td
                                       style="width:14%;font-weight:600;color:#000;font-size:14px;text-align:left;font-family:'calibri','Segoe UI','Helvetica Neue',Helvetica,Arial,sans-serif;padding:6px 0 6px 10px;border-right:1px solid #ddd;border-bottom:1px solid #ddd">
                                       Folio No</td>
                                    <td
                                       style="width:30%;color:#000;font-size:14px;text-align:left;font-family:'calibri','Segoe UI','Helvetica Neue',Helvetica,Arial,sans-serif;padding:6px 0 6px 10px;border-bottom:1px solid #ddd;border-right:1px solid #ddd">
                                       {{$data->folio_no}}</td>
                                 </tr>
                                 <tr>
                                    <td
                                       style="width:20%;font-weight:600;color:#000;font-size:14px;font-family:'calibri','Segoe UI','Helvetica Neue',Helvetica,Arial,sans-serif;padding:6px 0 6px 10px;border-right:1px solid #ddd;border-bottom:1px solid #ddd;border-left:1px solid #ddd">
                                       Query Received Date
                                    </td>
                                    <td
                                       style="width:34%;color:#000;font-size:14px;text-align:left;font-family:'calibri','Segoe UI','Helvetica Neue',Helvetica,Arial,sans-serif;padding:6px 0 6px 10px;border-right:1px solid #ddd;border-bottom:1px solid #ddd">
                                       {{date('d-m-Y',strtotime($data->date_time))}}</td>
                                    <td
                                       style="width:14%;font-weight:600;color:#000;font-size:14px;text-align:left;font-family:'calibri','Segoe UI','Helvetica Neue',Helvetica,Arial,sans-serif;padding:6px 0 6px 10px;border-right:1px solid #ddd;border-bottom:1px solid #ddd">
                                       Query Solved Date</td>
                                    <td
                                       style="width:30%;color:#000;font-size:14px;text-align:left;font-family:'calibri','Segoe UI','Helvetica Neue',Helvetica,Arial,sans-serif;padding:6px 0 6px 10px;border-bottom:1px solid #ddd;border-right:1px solid #ddd">
                                       {{isset($data->actual_close_date)?date('d-m-Y',strtotime($data->date_time)):"N/A"}}
                                    </td>
                                 </tr>
                                 <tr style="background:#f6f6f6">
                                    <td
                                       style="width:20%;font-weight:600;color:#000;font-size:14px;font-family:'calibri','Segoe UI','Helvetica Neue',Helvetica,Arial,sans-serif;padding:6px 0 6px 10px;border-right:1px solid #ddd;border-bottom:1px solid #ddd;border-left:1px solid #ddd">
                                       Expected Close Date
                                    </td>
                                    <td
                                       style="width:34%;color:#000;font-size:14px;text-align:left;font-family:'calibri','Segoe UI','Helvetica Neue',Helvetica,Arial,sans-serif;padding:6px 0 6px 10px;border-right:1px solid #ddd;border-bottom:1px solid #ddd">
                                       {{isset($data->expected_close_date)?date('d-m-Y',strtotime($data->date_time)):"N/A"}}
                                    </td>
                                    <td
                                       style="width:14%;font-weight:600;color:#000;font-size:14px;text-align:left;font-family:'calibri','Segoe UI','Helvetica Neue',Helvetica,Arial,sans-serif;padding:6px 0 6px 10px;border-right:1px solid #ddd;border-bottom:1px solid #ddd">
                                       Query TAT Expired</td>
                                    <td
                                       style="width:30%;color:#000;font-size:14px;text-align:left;font-family:'calibri','Segoe UI','Helvetica Neue',Helvetica,Arial,sans-serif;padding:6px 0 6px 10px;border-bottom:1px solid #ddd;border-right:1px solid #ddd">
                                    </td>
                                 </tr>
                                 <tr>
                                    <td valign="top"
                                       style="width:20%;font-weight:600;color:#000;font-size:14px;font-family:'calibri','Segoe UI','Helvetica Neue',Helvetica,Arial,sans-serif;padding:6px 0 6px 10px;border-right:1px solid #ddd;border-bottom:1px solid #ddd;border-left:1px solid #ddd">
                                       AMC Name
                                    </td>
                                    <td
                                       style="width:34%;color:#000;font-size:14px;text-align:left;font-family:'calibri','Segoe UI','Helvetica Neue',Helvetica,Arial,sans-serif;padding:6px 0 6px 10px;border-bottom:1px solid #ddd;border-right:1px solid #ddd">
                                       {{$data->allscheme[0]->schemename->amc_name}}</td>

                                    <td valign="top"
                                       style="width:20%;font-weight:600;color:#000;font-size:14px;font-family:'calibri','Segoe UI','Helvetica Neue',Helvetica,Arial,sans-serif;padding:6px 0 6px 10px;border-right:1px solid #ddd;border-bottom:1px solid #ddd;border-left:1px solid #ddd">
                                       Scheme Name
                                    </td>
                                    <td
                                       style="width:34%;color:#000;font-size:14px;text-align:left;font-family:'calibri','Segoe UI','Helvetica Neue',Helvetica,Arial,sans-serif;padding:6px 0 6px 10px;border-bottom:1px solid #ddd;border-right:1px solid #ddd">
                                       {{$data->allscheme[0]->schemename->scheme_name}}</td>
                                 </tr>
                                 <tr style="background:#f6f6f6">
                                    <td valign="top"
                                       style="width:20%;font-weight:600;color:#000;font-size:14px;font-family:'calibri','Segoe UI','Helvetica Neue',Helvetica,Arial,sans-serif;padding:6px 0 6px 10px;border-right:1px solid #ddd;border-bottom:1px solid #ddd;border-left:1px solid #ddd">
                                       Query Details
                                    </td>
                                    <td colspan="3"
                                       style="width:34%;color:#000;font-size:14px;text-align:left;font-family:'calibri','Segoe UI','Helvetica Neue',Helvetica,Arial,sans-serif;padding:6px 0 6px 10px;border-bottom:1px solid #ddd;border-right:1px solid #ddd">
                                       {{$data->query_details}}</td>
                                 </tr>
                                 <tr style="background:#f6f6f6">
                                    <td valign="top"
                                       style="width:20%;font-weight:600;color:#000;font-size:14px;font-family:'calibri','Segoe UI','Helvetica Neue',Helvetica,Arial,sans-serif;padding:6px 0 6px 10px;border-right:1px solid #ddd;border-bottom:1px solid #ddd;border-left:1px solid #ddd">
                                       Reply Description
                                    </td>
                                    <td colspan="3"
                                       style="width:34%;color:#000;font-size:14px;text-align:left;font-family:'calibri','Segoe UI','Helvetica Neue',Helvetica,Arial,sans-serif;padding:6px 0 6px 10px;border-bottom:1px solid #ddd;border-right:1px solid #ddd">
                                       {{$data->remarks}}</td>
                                 </tr>

                              </tbody>
                           </table>
                           <!-- <table width="100%" style="border-color:#000;width:100%">
                      <tbody><tr>
                         <td height="10"></td>
                      </tr>
                      <tr>
                         <td style="width:100%;font-weight:600;border-color:#000;font-family:'calibri','Segoe UI','Helvetica Neue',Helvetica,Arial,sans-serif;color:#000;font-size:14px;text-align:left;vertical-align:top">For any queries, our Customer Care Helpline is available to assist you from Monday to Saturday between 9:00 A.M. to 6:30 P.M. (except Holidays in Prudent) at 1800 419 9992 from registered Mobile No only.  </td>
                      </tr>
                   </tbody></table> -->
                        </td>
                        <td style="width:1%"></td>
                     </tr>
                     <tr>
                        <td height="20"></td>
                     </tr>
                  </tbody>
               </table>
               @if($query_status_id==5 || $query_status_id==7)
               <table cellpadding="0" cellspacing="0" style="width:100%">
                  <tbody>
                     <tr>
                        <td style="width:1%"></td>
                        <td style="width:98%">
                           <table cellpadding="0" cellspacing="0" style="width:100%">
                              <tbody>
                                 <tr>
                                    <td
                                       style="text-align: center;width:100%;font-weight:600;
                                color:#000;font-size:14px;
                                font-family:'calibri','Segoe UI','Helvetica Neue',Helvetica,Arial,sans-serif;
                                padding:3px;
                                border-right:1px solid #ddd;border-bottom:1px solid #ddd;border-left:1px solid #ddd;border-top:1px solid #ddd">
                                       Feedback
                                    </td>
                                 </tr>
                                 <tr>
                                    <td style="text-align: center;width:100%;font-weight:400;
                                color:#000;font-size:11px;
                                font-family:'calibri','Segoe UI','Helvetica Neue',Helvetica,Arial,sans-serif;
                                padding:3px;
                                border-right:1px solid #ddd;border-bottom:1px solid #ddd;border-left:1px solid #ddd;">
                                       Customer satisfaction is always a number one priority for us. Your feedback will
                                       help us make our services better.
                                    </td>
                                 </tr>
                                 <tr>
                                    <td style="text-align: center;width:100%;font-weight:400;
                                color:#000;font-size:11px;
                                font-family:'calibri','Segoe UI','Helvetica Neue',Helvetica,Arial,sans-serif;
                                padding:3px;
                                border-right:1px solid #ddd;border-bottom:1px solid #ddd;border-left:1px solid #ddd;">
                                       Please share your feedback click here- {{$data->feedback_url}} .
                                    </td>
                                 </tr>
                              </tbody>
                           </table>
                        </td>
                        <td style="width:1%"></td>
                     </tr>
                  </tbody>
               </table>
               @endif
               <table cellpadding="0" cellspacing="0" style="width:100%;margin-top: 5px;margin-bottom: 5px;">
                  <tbody>
                     <tr>
                        <td style="width:1%"></td>
                        <td style="width:98%">
                           <table cellpadding="0" cellspacing="0" style="width:100%">
                              <tbody>
                                 <tr>
                                    <td style="text-align: left;width:100%;font-weight:500;
                                color:#000;font-size:11px;
                                font-family:'calibri','Segoe UI','Helvetica Neue',Helvetica,Arial,sans-serif;
                                padding:3px">
                                       For any queries, our Customer care Helpline is available to assist you from
                                       Monday to Friday between 10:00 A.M to 6:00 P.M.(Except Holidays in NuEdge
                                       Corporate) at 9830939393 from registered Mobile No only.
                                    </td>
                              </tbody>
                           </table>
                        </td>
                        <td style="width:1%"></td>
                     </tr>
                  </tbody>
               </table>


               <table cellpadding="0" cellspacing="0" style="width:100%;margin-bottom: 5px;">
                  <tbody>
                     <tr>
                        <td style="width:1%"></td>
                        <td style="width:98%">
                           <table cellpadding="0" cellspacing="0" style="width:100%">
                              <tbody>
                                 <tr>
                                    <td style="text-align: center;width:100%;font-weight:500;
                                color:#000;font-size:11px;width:50%;border-right:2px solid #ddd;border-bottom:2px solid #ddd;border-left:2px solid #ddd;border-top:2px solid #ddd;
                                font-family:'calibri','Segoe UI','Helvetica Neue',Helvetica,Arial,sans-serif;
                                padding:3px">
                                       <h5 style="margin:0px;font-size:14px;">
                                          2000
                                       </h5>
                                       <h5 style="margin:0px;font-size:14px;">
                                          Happy Clients
                                       </h5>
                                    </td>
                                    <td style="text-align: center;width:100%;font-weight:500;
                                color:#000;font-size:11px;width:50%;border-right:2px solid #ddd;border-bottom:2px solid #ddd;border-top:2px solid #ddd;
                                font-family:'calibri','Segoe UI','Helvetica Neue',Helvetica,Arial,sans-serif;
                                padding:3px">
                                       <h5 style="margin:0px;;font-size:14px;">
                                          300 Crore*
                                       </h5>
                                       <h5 style="margin:0px;;font-size:14px;">
                                          Asset Under Management
                                       </h5>
                                       <h5 style="margin:0px;font-size:8px;">
                                          *As on 25.10.2024
                                       </h5>
                                    </td>
                              </tbody>
                           </table>
                        </td>
                        <td style="width:1%"></td>
                     </tr>
                  </tbody>
               </table>

               <table cellpadding="0 " cellspacing="0 " style="width:100%;border:solid 1px #ccc">
                  <tbody>
                     <tr>
                        <td>
                           <table border="0 " cellpadding="0 " cellspacing="0 " style="width:100%">
                              <tbody>
                                 <tr>
                                    <td align="center" height="20"></td>
                                 </tr>
                                 <tr>
                                    <td style="width:33.33%;border-right:1px solid #ccc;text-align:center">
                                       <p
                                          style="margin:0;font-family:'calibri','Segoe UI','Helvetica Neue',Helvetica,Arial,sans-serif;color:#000000">
                                          <small>For queries write us at</small>
                                       </p>
                                    </td>
                                    <td style="width:33.33%;border-right:1px solid #ccc;text-align:center">
                                       <p style="margin:0;font-family:Helvetica,'Open Sans',sans-serif;color:#000000">
                                          <small>We are on Mobile</small>
                                       </p>
                                    </td>
                                    <td style="width:33.33%;border-right:1px solid #ccc"></td>
                                 </tr>
                                 <tr>
                                    <td style="width:33.33%;border-right:1px solid #ccc;text-align:center">
                                       <p
                                          style="margin:0;font-family:Helvetica,Open Sans,sans-serif;color:#000000;font-size:14px">
                                       <div
                                          style="font-size:8px;margin-top:8px;font-weight:600;display: flex;align-items: center;justify-content: center;">
                                          <img
                                             src="https://img.freepik.com/free-vector/open-email-envelope_1020-530.jpg"
                                             height="15" />
                                          <a href="mailto:service@nuedgecorporate.co.in" target="_blank">
                                             service@nuedgecorporate.co.in</a>
                                       </div>
                                       <div
                                          style="font-size:11px;font-weight:600;display: flex;align-items: center;justify-content: center;">
                                          <div
                                             style="display: flex !important;align-items: center !important;justify-content: center !important;">
                                             <img
                                                src="https://t4.ftcdn.net/jpg/04/63/63/59/360_F_463635935_IweuYhCqZRtHp3SLguQL8svOVroVXvvZ.jpg"
                                                height="20" /> <span
                                                style="font-size: 8px;margin-left: 2px;margin-right: 6px;">Or</span>
                                             <img
                                                src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQ-1iNEAhFjtpAOIgxrnXOpXWBETEbtT3Dicg&s"
                                                height="15" />
                                             <p style="font-size: 10px;margin-left: 2px;margin-right: 6px;">
                                                9830939393</p>

                                          </div>
                                       </div>
                                       </p>
                                    </td>
                                    <td style="width:33.33%;border-right:1px solid #ccc;text-align:center">
                                       <p
                                          style="margin:0;font-family:Helvetica,'Open Sans',sans-serif;color:#000000;font-size:14px">
                                          <span style="margin-top:15px">
                                             <a href="http://sendgrid2.prudentcorporate.com/ls/click?upn=nNOyPkj5VShRSdIqNzHwWfPyoanPMm1V7wW8XxR-2Fp-2B08d2ozp2XXc9Psi5ASczflxlsOe4qBfHQ6WB9R-2F2hljQ-3D-3D0pFc_WyMfYEDImUyG26D8-2BUt1U6FBLqPxy2xUQbmOaHAZUv8T3fyMI8HD3JkNuY9IdyVuVfI1wZ6d70qef2PFlkILhauKs-2FxuBkeNt7hHGQdHxwxnI7gl5IeAt4QmSQ0oe5unQeYGVsSZzmwEEVBAL20uocsHnMhArtpfvGhnw4gwAKgAyLiJ2Y5YKEmnxiYhk-2Ff5iUqj9QyyAf-2BZ2P-2BylFVuTNMQCTfnE6mSKF8U40ckdRw-3D"
                                                style="text-decoration:none" target="_blank"
                                                data-saferedirecturl="https://www.google.com/url?q=http://sendgrid2.prudentcorporate.com/ls/click?upn%3DnNOyPkj5VShRSdIqNzHwWfPyoanPMm1V7wW8XxR-2Fp-2B08d2ozp2XXc9Psi5ASczflxlsOe4qBfHQ6WB9R-2F2hljQ-3D-3D0pFc_WyMfYEDImUyG26D8-2BUt1U6FBLqPxy2xUQbmOaHAZUv8T3fyMI8HD3JkNuY9IdyVuVfI1wZ6d70qef2PFlkILhauKs-2FxuBkeNt7hHGQdHxwxnI7gl5IeAt4QmSQ0oe5unQeYGVsSZzmwEEVBAL20uocsHnMhArtpfvGhnw4gwAKgAyLiJ2Y5YKEmnxiYhk-2Ff5iUqj9QyyAf-2BZ2P-2BylFVuTNMQCTfnE6mSKF8U40ckdRw-3D&amp;source=gmail&amp;ust=1730201120954000&amp;usg=AOvVaw1JBhuW2phTzqKTi1EqRuXh">
                                                <img
                                                   src="https://ci3.googleusercontent.com/meips/ADKq_NZF3kVNkAzcgCn82YR8JnjLO7qLMeZNuecDX678w7kKkQ2YuXsxfmSe90iYs7CIi7JaR5Lrdf34RRoEheDgPyr-DDeYFMjimeNuA_p1enFKNP47GLa_3oFpgVEsuc9TLNNzVNiN=s0-d-e1-ft#https://www.prudentcorporate.com/upload/ExpToPdf/PartnerMailImg/170816gplay.jpg"
                                                   class="CToWUd" data-bit="iit">
                                             </a>
                                             <a href="http://sendgrid2.prudentcorporate.com/ls/click?upn=nNOyPkj5VShRSdIqNzHwWWro9QQk-2FUXRej5oORWvnmIuskt17uoXsMevLnvO3h4KlnRJyj5TFp7LWujEOEv7e8EHm871q0VT-2BxLt1ZBiVJo-3DRIm8_WyMfYEDImUyG26D8-2BUt1U6FBLqPxy2xUQbmOaHAZUv8T3fyMI8HD3JkNuY9IdyVuVfI1wZ6d70qef2PFlkILhQxCU7XmdjIuUgG0SeSGbDZteytZ4rpykVGICg2EkVi0lrdDhRMZnG02vs2IqV9XAghI7gVFzOuPn0z9kHW9eHjdI-2F612K68eIWSn70sG7d7AmWvcYMbXHKxdj-2B-2FzR2nDV8QvfeFq5iSYdZQSXvwKL0-3D"
                                                style="text-decoration:none" target="_blank"
                                                data-saferedirecturl="https://www.google.com/url?q=http://sendgrid2.prudentcorporate.com/ls/click?upn%3DnNOyPkj5VShRSdIqNzHwWWro9QQk-2FUXRej5oORWvnmIuskt17uoXsMevLnvO3h4KlnRJyj5TFp7LWujEOEv7e8EHm871q0VT-2BxLt1ZBiVJo-3DRIm8_WyMfYEDImUyG26D8-2BUt1U6FBLqPxy2xUQbmOaHAZUv8T3fyMI8HD3JkNuY9IdyVuVfI1wZ6d70qef2PFlkILhQxCU7XmdjIuUgG0SeSGbDZteytZ4rpykVGICg2EkVi0lrdDhRMZnG02vs2IqV9XAghI7gVFzOuPn0z9kHW9eHjdI-2F612K68eIWSn70sG7d7AmWvcYMbXHKxdj-2B-2FzR2nDV8QvfeFq5iSYdZQSXvwKL0-3D&amp;source=gmail&amp;ust=1730201120954000&amp;usg=AOvVaw2n8JlxhMAXNplkmMFsz9Qv">
                                                <img
                                                   src="https://ci3.googleusercontent.com/meips/ADKq_NbM0OlTjHHFIZn3-eTMGuxlw8D_uP5RITGoiauKGh4qL9GYXICNZYQvAZwpiUALmSghLEdvo6zoQknKVEAmQ9ORuo9CmwFvsFepcS6bobnudn9PdCkrn1Isoilpig6aEIDNYumjrA=s0-d-e1-ft#https://www.prudentcorporate.com/upload/ExpToPdf/PartnerMailImg/20170310_ios.png"
                                                   class="CToWUd" data-bit="iit">
                                             </a>
                                          </span>
                                       </p>
                                    </td>
                                    <td style="width:33.33%;">
                                       <table style="text-align:center;width:100%" cellspacing="0 " cellpadding="0 "
                                          border="0 " bgcolor="#FFFFFF " align="center ">
                                          <tbody>
                                             <tr>
                                                <td width="8%">
                                                   <a href="http://sendgrid2.prudentcorporate.com/ls/click?upn=nNOyPkj5VShRSdIqNzHwWahKScqfUy7Goq2fahYNIzZNyNODqZj3OFLiVgR1Lg-2FlFpx50lVb7VCW-2BvIWtwgOj1LIIzsQrVIDZ2nZfA61eso-3DU2VT_WyMfYEDImUyG26D8-2BUt1U6FBLqPxy2xUQbmOaHAZUv8T3fyMI8HD3JkNuY9IdyVuVfI1wZ6d70qef2PFlkILhVhs5MjLir7k-2Biri-2BxW6e0hdanD2OoU4el4ztoDCSFN-2B1-2Fgg3ygilTqTOQbzISjWfoWtVbXkvN-2FpBo2krpSGXcgTY7e2ip2TqP7ME5KKE2C0PF5M57cYn-2FHuyP8mfTF2WjNu2W0tbs-2FSoxsATMMJ8Vc-3D"
                                                      target="_blank"
                                                      data-saferedirecturl="https://www.google.com/url?q=http://sendgrid2.prudentcorporate.com/ls/click?upn%3DnNOyPkj5VShRSdIqNzHwWahKScqfUy7Goq2fahYNIzZNyNODqZj3OFLiVgR1Lg-2FlFpx50lVb7VCW-2BvIWtwgOj1LIIzsQrVIDZ2nZfA61eso-3DU2VT_WyMfYEDImUyG26D8-2BUt1U6FBLqPxy2xUQbmOaHAZUv8T3fyMI8HD3JkNuY9IdyVuVfI1wZ6d70qef2PFlkILhVhs5MjLir7k-2Biri-2BxW6e0hdanD2OoU4el4ztoDCSFN-2B1-2Fgg3ygilTqTOQbzISjWfoWtVbXkvN-2FpBo2krpSGXcgTY7e2ip2TqP7ME5KKE2C0PF5M57cYn-2FHuyP8mfTF2WjNu2W0tbs-2FSoxsATMMJ8Vc-3D&amp;source=gmail&amp;ust=1730201120954000&amp;usg=AOvVaw0FxxyL_JFMhxAQv3KZmlB1">
                                                      <img alt=" "
                                                         src="https://ci3.googleusercontent.com/meips/ADKq_NY_yiskx0fu-bAEdmtNlZNskNXod8qgY1-5U9cvoFe7q27THxDLdck1lq-qFuRof7sMHfuVShL_5kyFRUHZTqDIz6ZUwShwnx5HwgwaSTfYxrNiGdon_pNhT2T7gtPSfLXn2D5atU4=s0-d-e1-ft#https://www.prudentcorporate.com/upload/ExpToPdf/PartnerMailImg/facebook13616.png"
                                                         class="CToWUd" data-bit="iit">
                                                   </a>
                                                </td>
                                                <td width="8%">
                                                   <a href="http://sendgrid2.prudentcorporate.com/ls/click?upn=nNOyPkj5VShRSdIqNzHwWcgSVxR1exBiCrSNIR4q5-2F2wX-2BJnccztMr78NICA42YiqcrRf2XwtTwXdNor0KKaVvoTfrx1JuoFe8w4ReOBYY8-3Dy78W_WyMfYEDImUyG26D8-2BUt1U6FBLqPxy2xUQbmOaHAZUv8T3fyMI8HD3JkNuY9IdyVuVfI1wZ6d70qef2PFlkILhTO09UJBddgtjPxTYGJS2JxLj2I-2FTps-2FuVWXfejCOxYuybH1tcSRKIdcR5GV1IDeAV5R6JxqVhRI9M0WO7SZJPumt2AQ2u1ZJSf9x8aEbve39QXDqgMQql5RoKeREnrTh1LWqdhlknwhOMn9uEqC1Qg-3D"
                                                      target="_blank"
                                                      data-saferedirecturl="https://www.google.com/url?q=http://sendgrid2.prudentcorporate.com/ls/click?upn%3DnNOyPkj5VShRSdIqNzHwWcgSVxR1exBiCrSNIR4q5-2F2wX-2BJnccztMr78NICA42YiqcrRf2XwtTwXdNor0KKaVvoTfrx1JuoFe8w4ReOBYY8-3Dy78W_WyMfYEDImUyG26D8-2BUt1U6FBLqPxy2xUQbmOaHAZUv8T3fyMI8HD3JkNuY9IdyVuVfI1wZ6d70qef2PFlkILhTO09UJBddgtjPxTYGJS2JxLj2I-2FTps-2FuVWXfejCOxYuybH1tcSRKIdcR5GV1IDeAV5R6JxqVhRI9M0WO7SZJPumt2AQ2u1ZJSf9x8aEbve39QXDqgMQql5RoKeREnrTh1LWqdhlknwhOMn9uEqC1Qg-3D&amp;source=gmail&amp;ust=1730201120954000&amp;usg=AOvVaw2DCGs-Yz7y0JLjDlD7HVbj">
                                                      <img alt=" "
                                                         src="https://ci3.googleusercontent.com/meips/ADKq_NYvrafooCLsu-DOm35B5qXQmCnUQSd_jetGnuzqNgOeeqts6AQjlH5NhMES97wE5uIYKQ_xdOs5yb66rF4kkJARILvwbKxF8ILHxZixnPlubTMk3HoPYfvIUmTd3eqvD6vSmgWLPRU=s0-d-e1-ft#https://www.prudentcorporate.com/upload/ExpToPdf/PartnerMailImg/linkedin13616.png"
                                                         class="CToWUd" data-bit="iit">
                                                   </a>
                                                </td>
                                                <td width="8%">
                                                   <a href="http://sendgrid2.prudentcorporate.com/ls/click?upn=nNOyPkj5VShRSdIqNzHwWY5FiQ6nAvUuS2JH81L-2BWTbcMX9zJSTyxU8KpqMLefiKjdvd_WyMfYEDImUyG26D8-2BUt1U6FBLqPxy2xUQbmOaHAZUv8T3fyMI8HD3JkNuY9IdyVuVfI1wZ6d70qef2PFlkILhYGxceln24cqnATXNAe8mS8EpPMW0tx-2FYtqUe37ze6ju4DiEwDDBUFdf3G10FSzr6WE0nUeJrfoJ2YU1Dcf90f94jvmouZMl7fvzkdptS-2BcOshvBxYxjl8M-2BtDKQbJbn4jJS0TVhq4W74wNrfw7zZyE-3D"
                                                      target="_blank"
                                                      data-saferedirecturl="https://www.google.com/url?q=http://sendgrid2.prudentcorporate.com/ls/click?upn%3DnNOyPkj5VShRSdIqNzHwWY5FiQ6nAvUuS2JH81L-2BWTbcMX9zJSTyxU8KpqMLefiKjdvd_WyMfYEDImUyG26D8-2BUt1U6FBLqPxy2xUQbmOaHAZUv8T3fyMI8HD3JkNuY9IdyVuVfI1wZ6d70qef2PFlkILhYGxceln24cqnATXNAe8mS8EpPMW0tx-2FYtqUe37ze6ju4DiEwDDBUFdf3G10FSzr6WE0nUeJrfoJ2YU1Dcf90f94jvmouZMl7fvzkdptS-2BcOshvBxYxjl8M-2BtDKQbJbn4jJS0TVhq4W74wNrfw7zZyE-3D&amp;source=gmail&amp;ust=1730201120954000&amp;usg=AOvVaw23UMzjCcV-5OUbR4fd1MCS">
                                                      <img alt=" "
                                                         src="https://i.pinimg.com/originals/8e/72/f7/8e72f7331b652b842b0c271ab144d332.png"
                                                         height="28" class="CToWUd" data-bit="iit">
                                                   </a>
                                                </td>
                                                <td width="8%">
                                                   <!-- <a href="http://sendgrid2.prudentcorporate.com/ls/click?upn=nNOyPkj5VShRSdIqNzHwWVxqHEB8X-2BDoFVIK7HC1dP-2BHaWzKMPLBejZ8M68P6L1nz3zWlOI4NqU-2B59SOz1WGDQ-3D-3DIk8Z_WyMfYEDImUyG26D8-2BUt1U6FBLqPxy2xUQbmOaHAZUv8T3fyMI8HD3JkNuY9IdyVuVfI1wZ6d70qef2PFlkILhSmAVBg3eoWDuMXENj-2FtyGmem0Z3kRdCQ6TKkK4Rl-2FSy6TG9m40RpgJ3tXVwEOHlImpj1fJ-2B5jesww-2BB7XF8rZL6IQ1uBYLOwOi93wJru1QR7A2hYdsXxi2RHAJ51RvHC9Ag2Kbe9D-2BXLeFd1AMnba4-3D" target="_blank" data-saferedirecturl="https://www.google.com/url?q=http://sendgrid2.prudentcorporate.com/ls/click?upn%3DnNOyPkj5VShRSdIqNzHwWVxqHEB8X-2BDoFVIK7HC1dP-2BHaWzKMPLBejZ8M68P6L1nz3zWlOI4NqU-2B59SOz1WGDQ-3D-3DIk8Z_WyMfYEDImUyG26D8-2BUt1U6FBLqPxy2xUQbmOaHAZUv8T3fyMI8HD3JkNuY9IdyVuVfI1wZ6d70qef2PFlkILhSmAVBg3eoWDuMXENj-2FtyGmem0Z3kRdCQ6TKkK4Rl-2FSy6TG9m40RpgJ3tXVwEOHlImpj1fJ-2B5jesww-2BB7XF8rZL6IQ1uBYLOwOi93wJru1QR7A2hYdsXxi2RHAJ51RvHC9Ag2Kbe9D-2BXLeFd1AMnba4-3D&amp;source=gmail&amp;ust=1730201120954000&amp;usg=AOvVaw1w-PzKkF0PAlVxEYJZHU-q">
                                        <img alt=" " height="20" src="" class="CToWUd" data-bit="iit">
                                        </a> -->
                                                </td>
                                                <td width="1%"></td>
                                             </tr>
                                          </tbody>
                                       </table>
                                    </td>
                                 </tr>
                                 <tr>
                                    <td align="center" height="20"></td>
                                 </tr>
                              </tbody>
                           </table>
                        </td>
                     </tr>
                  </tbody>
               </table>

               <table style="padding:0 10px;width:100%;border:solid 1px #ccc" cellspacing="0 " cellpadding="0 "
                  border="0 " bgcolor="#FFFFFF " align="center ">
                  <tbody>
                     <tr>
                        <td
                           style="padding-top:25px;padding-left:30px;padding-right:15px;padding-bottom:25px;font-family:'calibri','Segoe UI','Helvetica Neue',Helvetica,Arial,sans-serif;font-size:12px;line-height:15pt;color:#444">
                           <p
                              style="margin-top:0px;margin-bottom:0px!important;padding-top:0px;padding-bottom:10px;font-family:'calibri','Segoe UI','Helvetica Neue',Helvetica,Arial,sans-serif;font-size:15px;line-height:100%;color:#444;font-weight:bold">
                              Thank you,
                           </p>
                           <span
                              style="line-height:15pt;display:inline-block;font-family:'calibri','Segoe UI','Helvetica Neue',Helvetica,Arial,sans-serif">
                              <b>NuEdge Corporate Private Limited</b><br>
                              "1A, Dr Sarat Banerjee Road, <br>
                              Opp-CESE Supply Office<br>
                              Kolkata -700029. <br>
                              E-mail: <a href="mailto:customercare@prudentcorporate.com" style="text-decoration:none"
                                 target="_blank">service@nuedgecorporate.co.in</a>
                           </span>
                           <br>

                        </td>
                     </tr>
                     <tr>
                        <td style="background:white;padding:10pt 15pt 9pt 15pt;font-size: 12px;" valign="top"
                           align="center">
                           Disclaimer: Mutual Fund investments are subject to market risks, read all scheme related
                           documents carefully.
                        </td>
                     </tr>
                     <tr>
                        <td>&nbsp;</td>
                     </tr>
                  </tbody>
               </table>
            </td>
         </tr>
      </tbody>
   </table>
</body>

</html>