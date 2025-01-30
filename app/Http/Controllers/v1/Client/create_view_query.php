<?php
/*****************************************For AUM Report**********************************/

// CREATE VIEW `v_aum_report` AS
// SELECT td_mutual_fund_trans.rnt_id AS rnt_id,
// td_mutual_fund_trans.amc_code AS amc_code,
// td_mutual_fund_trans.folio_no AS folio_no,
// td_mutual_fund_trans.product_code AS product_code,
// td_mutual_fund_trans.trans_date AS trans_date,
// td_mutual_fund_trans.pur_price AS pur_price,
// td_mutual_fund_trans.units AS units,
// td_mutual_fund_trans.amount AS amount,
// td_mutual_fund_trans.stamp_duty AS stamp_duty,
// td_mutual_fund_trans.trxn_type AS trxn_type,
// td_mutual_fund_trans.trxn_type_flag AS trxn_type_flag,
// td_mutual_fund_trans.trxn_nature AS trxn_nature,
// td_mutual_fund_trans.trxn_type_code AS trxn_type_code,
// td_mutual_fund_trans.trxn_nature_code AS trxn_nature_code,
// td_mutual_fund_trans.trans_desc AS trans_desc,
// td_mutual_fund_trans.kf_trans_type AS kf_trans_type,
// td_mutual_fund_trans.trans_flag AS trans_flag,
// td_mutual_fund_trans.reinvest_flag AS reinvest_flag,
// td_mutual_fund_trans.dividend_option AS dividend_option,
// td_mutual_fund_trans.bu_type_flag AS bu_type_flag,
// td_mutual_fund_trans.bu_type_lock_flag AS bu_type_lock_flag,
// td_mutual_fund_trans.amc_flag AS amc_flag,
// td_mutual_fund_trans.scheme_flag AS scheme_flag,
// td_mutual_fund_trans.plan_option_flag AS plan_option_flag,
// td_mutual_fund_trans.divi_mismatch_flag AS divi_mismatch_flag,
// md_amc.amc_short_name AS amc_name,
// md_scheme.scheme_name AS scheme_name,
// md_category.cat_name as cat_name,
// md_subcategory.subcategory_name as subcat_name
// md_plan.plan_name AS plan_name,
// md_option.opt_name AS option_name,
// TRANS_TYPE_SUBTYPE(IF(td_mutual_fund_trans.rnt_id=1,(SELECT trans_type FROM md_mf_trans_type_subtype WHERE
// c_trans_type_code=td_mutual_fund_trans.trxn_type_code AND c_k_trans_type=td_mutual_fund_trans.trxn_type_flag AND
// c_k_trans_sub_type=td_mutual_fund_trans.trxn_nature_code LIMIT 1),
// (CASE
// WHEN td_mutual_fund_trans.trans_flag="DP" || td_mutual_fund_trans.trans_flag="DR" THEN (SELECT trans_type FROM
// md_mf_trans_type_subtype WHERE c_k_trans_sub_type=kf_trans_type AND k_divident_flag=td_mutual_fund_trans.trans_flag
// LIMIT 1)
// WHEN td_mutual_fund_trans.trans_flag="TO" THEN "Transfer Out"
// ELSE (SELECT trans_type FROM md_mf_trans_type_subtype WHERE c_k_trans_sub_type=kf_trans_type LIMIT 1)
// END)
// ),td_mutual_fund_trans.rnt_id,td_mutual_fund_trans.amount) AS transaction_type,
// TRANS_TYPE_SUBTYPE(IF(td_mutual_fund_trans.rnt_id=1,
// (SELECT trans_sub_type FROM md_mf_trans_type_subtype WHERE c_trans_type_code=td_mutual_fund_trans.trxn_type_code AND
// c_k_trans_type=td_mutual_fund_trans.trxn_type_flag AND c_k_trans_sub_type=td_mutual_fund_trans.trxn_nature_code LIMIT
// 1),
// (CASE
// WHEN td_mutual_fund_trans.trans_flag="DP" || td_mutual_fund_trans.trans_flag="DR" THEN (SELECT trans_sub_type FROM
// md_mf_trans_type_subtype WHERE c_k_trans_sub_type=kf_trans_type AND k_divident_flag=td_mutual_fund_trans.trans_flag
// LIMIT 1)
// WHEN td_mutual_fund_trans.trans_flag="TO" THEN "Transfer Out"
// ELSE (SELECT trans_sub_type FROM md_mf_trans_type_subtype WHERE c_k_trans_sub_type=kf_trans_type LIMIT 1)
// END)
// ),td_mutual_fund_trans.rnt_id,td_mutual_fund_trans.amount) AS transaction_subtype,
// SUM(td_mutual_fund_trans.units) AS tot_units,
// SUM(td_mutual_fund_trans.amount) AS tot_amount,
// SUM(td_mutual_fund_trans.stamp_duty) AS tot_stamp_duty,
// IF(td_mutual_fund_trans.tds!="",SUM(td_mutual_fund_trans.tds),0.00) AS tot_tds
// FROM td_mutual_fund_trans
// LEFT JOIN md_amc ON md_amc.amc_code = td_mutual_fund_trans.amc_code
// LEFT JOIN md_scheme_isin ON md_scheme_isin.product_code = td_mutual_fund_trans.product_code
// LEFT JOIN md_plan ON md_plan.id = md_scheme_isin.plan_id
// LEFT JOIN md_scheme ON md_scheme.id = md_scheme_isin.scheme_id
// LEFT JOIN md_option ON md_option.id = md_scheme_isin.option_id
// LEFT JOIN md_category ON md_category.id = md_scheme.category_id
// LEFT JOIN md_subcategory ON md_subcategory.id = md_scheme.subcategory_id
// WHERE td_mutual_fund_trans.delete_flag='N'
// AND td_mutual_fund_trans.amc_flag='N'
// AND td_mutual_fund_trans.scheme_flag='N'
// AND td_mutual_fund_trans.plan_option_flag='N'
// AND td_mutual_fund_trans.bu_type_flag='N'
// AND td_mutual_fund_trans.divi_mismatch_flag='N'
// GROUP BY td_mutual_fund_trans.trans_no,
// td_mutual_fund_trans.trxn_type_flag,
// td_mutual_fund_trans.trxn_nature_code,
// td_mutual_fund_trans.trans_desc,
// td_mutual_fund_trans.kf_trans_type,
// td_mutual_fund_trans.trans_flag,
// td_mutual_fund_trans.pur_price
// ORDER BY td_mutual_fund_trans.trans_date ASC

/*******************************************************************************************************/

// CREATE VIEW `v_broker_change_report` AS 
// SELECT tt_broker_change_trans_report.rnt_id AS rnt_id,
// tt_broker_change_trans_report.amc_code AS amc_code,
// tt_broker_change_trans_report.folio_no AS folio_no,
// tt_broker_change_trans_report.product_code AS product_code,
// tt_broker_change_trans_report.trans_date AS trans_date,
// tt_broker_change_trans_report.pur_price AS pur_price,
// tt_broker_change_trans_report.units AS units,
// tt_broker_change_trans_report.amount AS amount,
// tt_broker_change_trans_report.stamp_duty AS stamp_duty,
// tt_broker_change_trans_report.trxn_type AS trxn_type,
// tt_broker_change_trans_report.trxn_type_flag AS trxn_type_flag,
// tt_broker_change_trans_report.trxn_nature AS trxn_nature,
// tt_broker_change_trans_report.trxn_type_code AS trxn_type_code,
// tt_broker_change_trans_report.trxn_nature_code AS trxn_nature_code,
// tt_broker_change_trans_report.trans_desc AS trans_desc,
// tt_broker_change_trans_report.kf_trans_type AS kf_trans_type,
// tt_broker_change_trans_report.trans_flag AS trans_flag,
// tt_broker_change_trans_report.reinvest_flag AS reinvest_flag,
// tt_broker_change_trans_report.dividend_option AS dividend_option,
// tt_broker_change_trans_report.bu_type_flag AS bu_type_flag,
// tt_broker_change_trans_report.bu_type_lock_flag AS bu_type_lock_flag,
// tt_broker_change_trans_report.amc_flag AS amc_flag,
// tt_broker_change_trans_report.scheme_flag AS scheme_flag,
// tt_broker_change_trans_report.plan_option_flag AS plan_option_flag,
// tt_broker_change_trans_report.divi_mismatch_flag AS divi_mismatch_flag,
// md_amc.amc_name AS amc_name,
// md_scheme.scheme_name AS scheme_name,
// md_category.cat_name as cat_name,
// md_subcategory.subcategory_name as subcat_name
// md_plan.plan_name AS plan_name,
// md_option.opt_name AS option_name,
// TRANS_TYPE_SUBTYPE(IF(tt_broker_change_trans_report.rnt_id=1,(SELECT trans_type FROM md_mf_trans_type_subtype WHERE
// c_trans_type_code=tt_broker_change_trans_report.trxn_type_code AND c_k_trans_type=tt_broker_change_trans_report.trxn_type_flag AND
// c_k_trans_sub_type=tt_broker_change_trans_report.trxn_nature_code LIMIT 1),
// (CASE
// WHEN tt_broker_change_trans_report.trans_flag="DP" || tt_broker_change_trans_report.trans_flag="DR" THEN (SELECT trans_type FROM
// md_mf_trans_type_subtype WHERE c_k_trans_sub_type=kf_trans_type AND k_divident_flag=tt_broker_change_trans_report.trans_flag
// LIMIT 1)
// WHEN tt_broker_change_trans_report.trans_flag="TO" THEN "Transfer Out"
// ELSE (SELECT trans_type FROM md_mf_trans_type_subtype WHERE c_k_trans_sub_type=kf_trans_type LIMIT 1)
// END)
// ),tt_broker_change_trans_report.rnt_id,tt_broker_change_trans_report.amount) AS transaction_type,
// TRANS_TYPE_SUBTYPE(IF(tt_broker_change_trans_report.rnt_id=1,
// (SELECT trans_sub_type FROM md_mf_trans_type_subtype WHERE c_trans_type_code=tt_broker_change_trans_report.trxn_type_code AND
// c_k_trans_type=tt_broker_change_trans_report.trxn_type_flag AND c_k_trans_sub_type=tt_broker_change_trans_report.trxn_nature_code LIMIT
// 1),
// (CASE
// WHEN tt_broker_change_trans_report.trans_flag="DP" || tt_broker_change_trans_report.trans_flag="DR" THEN (SELECT trans_sub_type FROM
// md_mf_trans_type_subtype WHERE c_k_trans_sub_type=kf_trans_type AND k_divident_flag=tt_broker_change_trans_report.trans_flag
// LIMIT 1)
// WHEN tt_broker_change_trans_report.trans_flag="TO" THEN "Transfer Out"
// ELSE (SELECT trans_sub_type FROM md_mf_trans_type_subtype WHERE c_k_trans_sub_type=kf_trans_type LIMIT 1)
// END)
// ),tt_broker_change_trans_report.rnt_id,tt_broker_change_trans_report.amount) AS transaction_subtype,
// SUM(tt_broker_change_trans_report.units) AS tot_units,
// SUM(tt_broker_change_trans_report.amount) AS tot_amount,
// SUM(tt_broker_change_trans_report.stamp_duty) AS tot_stamp_duty,
// IF(tt_broker_change_trans_report.tds!="",SUM(tt_broker_change_trans_report.tds),0.00) AS tot_tds
// FROM tt_broker_change_trans_report
// LEFT JOIN md_amc ON md_amc.amc_code = tt_broker_change_trans_report.amc_code
// LEFT JOIN md_scheme_isin ON md_scheme_isin.product_code = tt_broker_change_trans_report.product_code
// LEFT JOIN md_plan ON md_plan.id = md_scheme_isin.plan_id
// LEFT JOIN md_scheme ON md_scheme.id = md_scheme_isin.scheme_id
// LEFT JOIN md_option ON md_option.id = md_scheme_isin.option_id
// LEFT JOIN md_category ON md_category.id = md_scheme.category_id
// LEFT JOIN md_subcategory ON md_subcategory.id = md_scheme.subcategory_id
// WHERE tt_broker_change_trans_report.rnt_id=1
// AND tt_broker_change_trans_report.delete_flag='N'
// AND tt_broker_change_trans_report.amc_flag='N'
// AND tt_broker_change_trans_report.scheme_flag='N'
// AND tt_broker_change_trans_report.plan_option_flag='N'
// AND tt_broker_change_trans_report.bu_type_flag='N'
// AND tt_broker_change_trans_report.divi_mismatch_flag='N'
// GROUP BY tt_broker_change_trans_report.trans_no,
// tt_broker_change_trans_report.trxn_type_flag,
// tt_broker_change_trans_report.trxn_nature_code,
// tt_broker_change_trans_report.trans_desc,
// tt_broker_change_trans_report.kf_trans_type,
// tt_broker_change_trans_report.trans_flag,
// tt_broker_change_trans_report.pur_price
// ORDER BY tt_broker_change_trans_report.trans_date ASC

/****************************************************************** *******************************************/
// CREATE VIEW v_v_aum_report AS SELECT 
// rnt_id             AS rnt_id,
//   amc_code           AS amc_code,
//   folio_no           AS folio_no,
//   product_code       AS product_code,
//   trans_date         AS trans_date,
//   pur_price          AS pur_price,
//   units              AS units,
//   amount             AS amount,
//   stamp_duty         AS stamp_duty,
//   trxn_type          AS trxn_type,
//   trxn_type_flag     AS trxn_type_flag,
//   trxn_nature        AS trxn_nature,
//   trxn_type_code     AS trxn_type_code,
//   trxn_nature_code   AS trxn_nature_code,
//   trans_desc         AS trans_desc,
//   kf_trans_type      AS kf_trans_type,
//   trans_flag         AS trans_flag,
//   reinvest_flag      AS reinvest_flag,
//   dividend_option    AS dividend_option,
//   bu_type_flag       AS bu_type_flag,
//   bu_type_lock_flag  AS bu_type_lock_flag,
//   amc_flag           AS amc_flag,
//   scheme_flag        AS scheme_flag,
//   plan_option_flag   AS plan_option_flag,
//   divi_mismatch_flag AS divi_mismatch_flag,
//   amc_name                  AS amc_name,
//   scheme_name                   AS scheme_name,
//   cat_name                    AS cat_name,
//   subcat_name         AS subcat_name,
//   plan_name                       AS plan_name,
//   option_name                     AS option_name,
// transaction_type AS transaction_type,
// transaction_subtype AS transaction_subtype,
// tot_units AS tot_units,
// tot_amount AS tot_amount,
// tot_stamp_duty AS tot_stamp_duty,
// tot_tds AS tot_tds,
// (CASE 
// 	WHEN transaction_subtype LIKE '%Rejection%' THEN 'Other'
// 	WHEN transaction_subtype LIKE "%Purchase%" || transaction_subtype LIKE "%Switch In%" || transaction_subtype LIKE "%Dividend Reinvestment%" || transaction_subtype LIKE "%STP In%" THEN "in"
// 	WHEN transaction_subtype LIKE "%Redemption%" || transaction_subtype LIKE "%Switch Out%" || transaction_subtype LIKE "%Transfer Out%" || transaction_subtype LIKE "%SWP%" || transaction_subtype LIKE "%STP Out%" THEN "out"
// END) AS in_out
// FROM v_aum_report WHERE amc_code='116'

/***********************************************************************************************************/

// CREATE VIEW v_v_v_aum_report AS
// WITH 
// cte1 AS (
//     SELECT product_code, SUM(tot_units * (in_out = 'out')) went_out
//     FROM v_v_aum_report
//     GROUP BY product_code
// ),
// cte2 AS (
//     SELECT *, SUM(tot_units * (in_out = 'in')) OVER (PARTITION BY product_code ORDER BY trans_date) cal_amount
//     FROM v_v_aum_report
// )
// SELECT rnt_id,amc_code,folio_no,product_code,trans_date,pur_price,units,amount,stamp_duty,
// amc_name,scheme_name,cat_name,subcat_name,plan_name,option_name,tot_units,tot_amount,tot_stamp_duty,tot_tds,in_out,
// CASE WHEN cal_amount - tot_units < in_out THEN cal_amount - in_out ELSE tot_units END result
// FROM cte1
// JOIN cte2 USING (product_code)
// WHERE in_out = 'in'
// AND in_out < cal_amount
// ORDER BY trans_date ASC;


/******************process********************/
// trnas table and broker table data together.
// set transction type and subtype
// set in_out


// SELECT * FROM `td_mutual_fund_trans` where folio_no='77720355034';

/****************************** */
// id
// mailback_process_id
// rnt_id
// arn_no
// sub_brk_cd
// euin_no
// old_euin_no
// first_client_name
// first_client_pan
// amc_code
// folio_no
// product_code
// trans_no
// trans_mode
// trans_status
// user_trans_no
// trans_date
// post_date
// pur_price
// units
// amount
// rec_date
// trxn_type
// trxn_type_flag
// trxn_nature
// trxn_type_code
// trxn_nature_code
// trans_desc
// kf_trans_type
// trans_flag
// te_15h
// micr_code
// sw_flag
// old_folio
// seq_no
// stt
// stamp_duty
// tds
// acc_no
// bank_name
// remarks
// reinvest_flag
// dividend_option
// isin_no
// bu_type_flag
// bu_type_lock_flag
// amc_flag
// scheme_flag
// plan_option_flag
// divi_mismatch_flag
// divi_lock_flag
// delete_flag
// deleted_at
// deleted_date
// portfolio_show_flag
// created_at
// updated_at



// SELECT admin_nuedge.td_mutual_fund_trans_merge.product_code,SUM(admin_nuedge.td_mutual_fund_trans_merge.amount)AS tot_amount,
// (SELECT admin_nav.td_nav_details.nav FROM admin_nav.td_nav_details 
// WHERE admin_nav.td_nav_details.product_code=admin_nuedge.td_mutual_fund_trans_merge.product_code LIMIT 1)AS  nav
// FROM admin_nuedge.td_mutual_fund_trans_merge GROUP BY admin_nuedge.td_mutual_fund_trans_merge.product_code

/*SELECT 
    mydatabase1.td_mutual_fund_trans_merge.UserID, 
    mydatabse2.tblUsers.UserID
FROM 
   mydatabase1.tblUsers
       INNER JOIN mydatabase2.tblUsers 
           ON mydatabase1.tblUsers.UserID = mydatabase2.tblUsers.UserID */
           
           

        //    WITH 
        //    cte1 AS (
        //        SELECT product_code, SUM(tot_units * (in_out = 'out')) went_out
        //        FROM v_v_aum_report
        //        GROUP BY product_code
        //    ),
        //    cte2 AS (
        //        SELECT *, SUM(tot_units * (in_out = 'in')) OVER (PARTITION BY product_code ORDER BY trans_date) cal_units
        //        FROM v_v_aum_report
        //    )
        //    SELECT rnt_id,amc_code,folio_no,product_code,trans_date,pur_price,units,amount,stamp_duty,
        //    amc_name,scheme_name,cat_name,subcat_name,plan_name,option_name,tot_units,tot_amount,tot_stamp_duty,tot_tds,in_out,
        //    CASE WHEN cal_units - tot_units < in_out THEN cal_units - in_out ELSE tot_units END result
        //    FROM cte1
        //    JOIN cte2 USING (product_code)
        //    WHERE in_out = 'in'
        //    AND in_out < cal_units
        //    ORDER BY trans_date ASC;
           

        //    WITH 
        //    `cte1` AS (SELECT `v_v_aum_report`.`product_code` AS `product_code`,SUM((`v_v_aum_report`.`tot_units` * (`v_v_aum_report`.`in_out` = 'out'))) AS `went_out` FROM `v_v_aum_report` GROUP BY `v_v_aum_report`.`product_code` ORDER BY `v_v_aum_report`.`trans_date`), 
        //     `cte2` AS (SELECT `v_v_aum_report`.`rnt_id` AS `rnt_id`,`v_v_aum_report`.`amc_code` AS `amc_code`,`v_v_aum_report`.`folio_no` AS `folio_no`,`v_v_aum_report`.`product_code` AS `product_code`,`v_v_aum_report`.`trans_date` AS `trans_date`,`v_v_aum_report`.`pur_price` AS `pur_price`,`v_v_aum_report`.`units` AS `units`,`v_v_aum_report`.`amount` AS `amount`,`v_v_aum_report`.`stamp_duty` AS `stamp_duty`,`v_v_aum_report`.`trxn_type` AS `trxn_type`,`v_v_aum_report`.`trxn_type_flag` AS `trxn_type_flag`,`v_v_aum_report`.`trxn_nature` AS `trxn_nature`,`v_v_aum_report`.`trxn_type_code` AS `trxn_type_code`,`v_v_aum_report`.`trxn_nature_code` AS `trxn_nature_code`,`v_v_aum_report`.`trans_desc` AS `trans_desc`,`v_v_aum_report`.`kf_trans_type` AS `kf_trans_type`,`v_v_aum_report`.`trans_flag` AS `trans_flag`,`v_v_aum_report`.`reinvest_flag` AS `reinvest_flag`,`v_v_aum_report`.`dividend_option` AS `dividend_option`,`v_v_aum_report`.`bu_type_flag` AS `bu_type_flag`,`v_v_aum_report`.`bu_type_lock_flag` AS `bu_type_lock_flag`,`v_v_aum_report`.`amc_flag` AS `amc_flag`,`v_v_aum_report`.`scheme_flag` AS `scheme_flag`,`v_v_aum_report`.`plan_option_flag` AS `plan_option_flag`,`v_v_aum_report`.`divi_mismatch_flag` AS `divi_mismatch_flag`,`v_v_aum_report`.`amc_name` AS `amc_name`,`v_v_aum_report`.`scheme_name` AS `scheme_name`,`v_v_aum_report`.`cat_name` AS `cat_name`,`v_v_aum_report`.`subcat_name` AS `subcat_name`,`v_v_aum_report`.`plan_name` AS `plan_name`,`v_v_aum_report`.`option_name` AS `option_name`,`v_v_aum_report`.`transaction_type` AS `transaction_type`,`v_v_aum_report`.`transaction_subtype` AS `transaction_subtype`,`v_v_aum_report`.`tot_units` AS `tot_units`,`v_v_aum_report`.`tot_amount` AS `tot_amount`,`v_v_aum_report`.`tot_stamp_duty` AS `tot_stamp_duty`,`v_v_aum_report`.`tot_tds` AS `tot_tds`,`v_v_aum_report`.`in_out` AS `in_out`,SUM((`v_v_aum_report`.`tot_units` * (`v_v_aum_report`.`in_out` = 'in'))) OVER (PARTITION BY `v_v_aum_report`.`product_code` ORDER BY `v_v_aum_report`.`trans_date` )  AS `cal_amount` FROM `v_v_aum_report`) 
        //     SELECT `cte2`.`rnt_id` AS `rnt_id`,`cte2`.`amc_code` AS `amc_code`,`cte2`.`folio_no` AS `folio_no`,`cte1`.`product_code` AS `product_code`,`cte2`.`trans_date` AS `trans_date`,`cte2`.`pur_price` AS `pur_price`,`cte2`.`units` AS `units`,`cte2`.`amount` AS `amount`,`cte2`.`stamp_duty` AS `stamp_duty`,`cte2`.`amc_name` AS `amc_name`,`cte2`.`scheme_name` AS `scheme_name`,`cte2`.`cat_name` AS `cat_name`,`cte2`.`subcat_name` AS `subcat_name`,`cte2`.`plan_name` AS `plan_name`,`cte2`.`option_name` AS `option_name`,`cte2`.`tot_units` AS `tot_units`,`cte2`.`tot_amount` AS `tot_amount`,`cte2`.`tot_stamp_duty` AS `tot_stamp_duty`,`cte2`.`tot_tds` AS `tot_tds`,`cte2`.`in_out` AS `in_out`,
        //     (CASE WHEN ((`cte2`.`cal_amount` - `cte2`.`tot_units`) < `cte2`.`in_out`) THEN (`cte2`.`cal_amount` - `cte2`.`in_out`) ELSE `cte2`.`tot_units` END) AS `result` 
        //     FROM (`cte1` JOIN `cte2` ON((`cte1`.`product_code` = `cte2`.`product_code`))) 
        //     WHERE ((`cte2`.`in_out` = 'in') 
        //     AND (`cte2`.`in_out` < `cte2`.`cal_amount`)) 
        //     ORDER BY `cte2`.`trans_date)
            

        // Y
        // [
        //     {
        //     "id":"N",
        //     "value":"KYC REGISTERED - New KYC"
        //     },
        //     {
        //         "id":"R",
        //         "value":"KYC Rejected"
        //     },
        //     {
        //         "id":"M",
        //         "value":"Modified KYC Rejected"
        //     },
        //       {
        //         "id":"I",
        //         "value":"Incomplete KYC"
        //     },
        //     {
        //       "id":"C",
        //       "value":"CVLMF KYC"
        //     },
        //     {
        //       "id":"F",
        //       "value":"No Data Found"
        //     }
        // ]
        // N
        // [
        //     {
        //       "id":"N",
        //       "value":"KYC REGISTERED - New KYC"
        //     },
        //     {
        //       "id":"V",
        //       "value":"KYC Validated"
        //     }
        //   ]
        //   .