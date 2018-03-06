<?php 
	namespace Home\Controller;
	use Think\Controller;
/**
 * 基础信息
 * 教研信息
 * 科研信息
 * 
 */
class BasicInfoController extends Controller{
	/** 
	 * 基础信息
	 * "userinfo" : {
	 * 	 	"gid" : 		"1000002323"  			工号， 
	 * 	 	"name" : 		"张三"      			姓名，
	 * 	 	"password" : 	"1000002323"  			密码（不用显示）
	 * 	 	"accountNum" : 	"0"       				账户编号
	 * 	 	"title" :   	"教授"   		 		职称
	 * 	 	"dept" ：		"计算机科学与技术""		专业
	 * 	 	"sex" : 		"男"   					性别
	 * 	 	"birthday" : 	"1970.01.23"     		生日
	 * 	 	"idCard" : 		"141125197001235669"    身份证号码
	 * 	 	"email" :		"2343243@163.com"       邮箱
	 * 	 	"mobile" : 		"15663658888"       	手机号码
	 * 	 	"telephone" : 	"0451-12365478"    		座机号码
	 * 	 	"country" :  	"中国""           		国籍
	 * 	 	"nation" :   	"汉族""            		民族
	 * 	 	"origin" : 		"黑龙江省哈尔滨市""   	籍贯
	 * 	 	"education" : 	"博士研究生""     		学历
	 * 	 	"iswork" :     	"是"          			是否在职
	 * }
	 */
	public function s(){
		phpinfo();
	}
	public function get_info(){

		
			$gid = session('gid');
			//$gid = 130001;              
			if($gid === null){ //session为空返回0
				$this->ajaxReturn(0);
			}else{
				$teacher = D('Teacher');
				$userinfo = $teacher->getInfo($gid);
				//echo '<pre>';
				//print_r($userinfo);exit;
				//返回教师信息
				$this->ajaxReturn($userinfo[0]);
			}
	
	}

	/**
	 * 教研信息：{
	 * 		教师教学奖
	 * 		教学团队
	 * 		教改项目
	 * 		教育教学研究论文
	 * 		教材
	 * 		指导优秀学位论文
	 * 		大学生科技创新项目及创新竞赛
	 * }
	 */
	
	/**
	 * 教师教学奖
	 * "teachaword_data": {
	 * 		"0":{
	 * 			"name" :  		"教学成果二等奖"    		名称
	 * 		 	"unit" :  		"东北林业大学"				批准单位
	 * 		  	"permitdate" : 	"20150601" 					批准时间
	 * 		    "category" : 	"校级教学成果奖" 			类别
	 * 		    "score" ： 		50 							得分
	 * 		    "submittime" : 	"2015-2016-2"				学期
	 *       },
	 *       "1":{	
	 *        	"name" :  		"于鸣"    							名称
	 * 		 	"unit" :  		"东北林业大学"						批准单位
	 * 		  	"permitdate" : 	"20150601" 							批准时间
	 * 		    "category" : 	"校级青年教师授课大赛二等奖" 		类别
	 * 		    "score" ： 		20 									得分
	 * 		    "submittime" : 	"2015-2016-2"						学期
	 *        }
	 * 		.....
	 * }
	 */
	public function teach_aword(){
		
			$gid = session('gid');
			if($gid === null){
				$this->ajaxReturn(0);
			}
			$teachaword = D('Teachteam');
			$teachaword_data = $teachaword->getTeachaword($gid);
			$this->ajaxReturn($teachaword_data);

	}
	/**
	 *教学团队
	 "teachteam_data": {
	 * 		"0":{
	 * 			"name" :  		"数据结构"    		名称
	 * 		 	"unit" :  		"东北林业大学"		批准单位
	 * 		  	"permitdate" : 	"20150601" 			批准时间
	 * 		    "category" : 	"校级教学团队" 		类别
	 * 		    "score" ： 		50 					得分
	 * 		    "submittime" : 	"2015-2016-2"		学期
	 *       },
	 *       "1":{	
	 *        	"name" :  		"《Web程序设计》重点课程建设"    		名称
	 * 		 	"unit" :  		"东北林业大学"						批准单位
	 * 		  	"permitdate" : 	"20150601" 							批准时间
	 * 		    "category" : 	"校级教学团队" 							类别
	 * 		    "score" ： 		20 										得分
	 * 		    "submittime" : 	"2015-2016-2"							学期
	 *        }
	 * 		.....
	 */
	public function teach_team(){
	
			$gid = session('gid');
			if($gid === null){
				$this->ajaxReturn(0);
			}
			$teachteam = D('Teachteam');
			$teachteam_data = $teachteam->getTeachteam($gid);
			$this->ajaxReturn($teachteam_data);

	}
		/**
		 *	指导优秀学位论文
		 *	"great_thesisdata" : {
		 * 		"0":{
		 * 			"name" :  		"白杰云"    		名称
		 * 		 	"unit" :  		"东北林业大学"		批准单位
		 * 		  	"permitdate" : 	"20150601" 			批准时间
		 * 		    "category" : 	"校级优秀硕士论文" 		类别
		 * 		    "score" ： 		60					得分
		 * 		    "submittime" : 	"2015-2016-2"		学期
		 *       },
		 *       "1":{	
		 *        	"name" :  		"心肌细胞的传导及心电图仿真"    		名称
		 * 		 	"unit" :  		"东北林业大学"						批准单位
		 * 		  	"permitdate" : 	"20150601" 							批准时间
		 * 		    "category" : 	"校级优秀本科论文" 							类别
		 * 		    "score" ： 		20 										得分
		 * 		    "submittime" : 	"2015-2016-2"							学期
		 *        }
		 * 		.....
		 *	}
		 * 
		 */
		public function teach_thesis(){
		
				$gid = session('gid');
				if($gid === null){
					$this->ajaxReturn(0);
				}
				$teach_greatthesis = D('Teachteam');
				$great_thesisdata = $teach_greatthesis->getTeachthesis($gid);
				$this->ajaxReturn($great_thesisdata);

		}
	/**
	 *教改项目
	 *	"teacherform_data" : {
	 *	"0" : {
	 *		教改项目名称  "name" 	: "远程多终端现场总线实验系统研建"
	 *		项目编号 	  "num"  	: "27"
	 *		学校财务编号  "finum"	: "27"
	 *		项目来源      "origin"  : "校实验技术开发和自制设备研发的立项课题"
	 *		开始时间      "begindate"   : "20120602"
	 *		结束时间	  "enddate"  	: "20130602"
	 *		审批经费	  "permitfee" : 0.2
	 *		已到账经费    "getfee"    : 0.2
	 *		本年到账经费  "yearfee"	  : 0.2
	 *		本年到账时间  "yeardate"  : "20120601"
	 *		本校排名	  "rank"      : 1
	 *		类别		  "categoryid"  : "校级项目"
	 *		得分		  "score"     : 20
	 *		学期		  "submitdate" : "2011-2012-2"
	 *		是否首次拨款  "isfirstmoney" : 是
	 *	}	
	 *	"1" : {
	 *		
	 *	
	 *	}
	 *	}
	 */
	public function teachform(){
	
			$gid = session('gid');
			if($gid === null){
				$this->ajaxReturn(0);
			}
			$teacherform = D('Teacherform');
			$teacherform_data = $teachform->getTeacherform($gid);
			$this->ajaxReturn($teacherform_data);
		
	}
	/**
	 *	教育教学研究论文
	 *	"teachthesis_data" : {
	 *				"0" : {
	 *	论文名称 		"name" :  "教学质量监控体系中教学方法的创新研究"
	 *	出版社			"publisher" : "教育学"
	 *	出版时间		"publishdate" : "20140501"
	 *	我校排名		"rank" : 1
	 * 	类别			"categoryid" : "教育教学研究论文"
	 * 	得分			"score"   :  20
	 * 	学期			"submitdate" : "2013-2014-2"
	 * 	索引号			"searchid"  : "231431434"
	 * 	期卷号			"chapterno" : "2014,5"
	 * 	起始页码		"startpage"  : "68"
	 * 	终止页码		"endpage"    :  "70"
	 *				}
	 *	 	"1" : {
	 *	 	
	 *	 	}
	 *	}
	 * 
	 */
	public function teachthesis(){
	
			$gid = session('gid');
			if($gid === null){
				$this->ajaxReturn(0);
			}
			$teachthesis = D('Thesis');
			$teachthesis_data = $teachthesis->getTeachthesis($gid);
			$this->ajaxReturn($teachthesis_data);
	
	}
	/**
	 * 教材
	 * 	"teachmaterial" : {
	 * 		"0" : {
	 * 教材名称  "name" : "现代信息技术与创新方法"
	 * 出版编号  "num"  : "ISBN 978-7-04-031501-1"
	 * 出版社    "publisher"  : "高等教育出版社"
	 * 出版日期  "publishdate" : "20110311"
	 * 字数      "count"  : 63
	 * 类别		 "categoryid"  : "正式出版教材"
	 * 得分 	 "score"  :  126
	 * 学期		 "submitdate"  : "2010-2011-2"
	 * 		}
	 * 	 	"1" : {
	 * 	 	
	 * 	 	}
	 * 	}
	 * 	  
	 */
	public function teachmaterial(){
	
			$gid = session('gid');
			if($gid === null){
				$this->ajaxReturn(0);
			}
			$teachmaterial = D('Teachbook');
			$teachmaterial_data = $teachmaterial -> getTeachmaterial($gid);
			$this->ajaxReturn($teachmaterial_data);
	
	}
	/**
	 *	大学生科技创新项目与创新竞赛
	 *	"compete_data"  : {
	 *		"0" : {
	 *	项目或竞赛名称		"name" : "2010ACM东北地区竞赛"
	 *	批准单位			"unit"	: "ACM中国竞赛组委会"
	 *	批准时间			"permitdate" : "20100601"
	 *	类别 				"categoryid" : "国家级学生科技学术竞赛三等奖指导老师"
	 *	得分				"score"    : 25
	 *	学期				"submitdate_id" : "2010-2011-1"
	 *		}
	 *		"1" : {
	 *	项目或竞赛名称		"name" : "黑龙江赛区“IEEE标准电脑鼠走迷宫”竞赛"
	 *	批准单位			"unit"	: "东北林业大学教务处"
	 *	批准时间			"permitdate" : "20100601"
	 *	类别 				"categoryid" : "省级(或企业承办的全国性竞赛)竞赛二等奖指导老师"
	 *	得分				"score"    : 15
	 *	学期				"submitdate_id" : "2010-2011-1"
	 *		}
	 *		
	 *	}	 
	 */
	public function compete(){
	
			$gid = session('gid');
			//$gid = 130001;
			if($gid === null){
				$this->ajaxReturn(0);
			}
			$compete = D('Compete');
			$compete_data = $compete -> getCompete($gid);

		    $this->ajaxReturn($compete_data);
	
	}
	/**
	 *	科研信息{
	 *		专著
	 *		科研项目
	 *		创新团队
	 *		优秀人才计划
	 *		成果及知识产权
	 *		科研论文及学术交流
	 *		}
	 */

	/**
	 * 专著
	 * "teachmonograph_data" : {
	 * 		"0" : {
	 * 专著名称  "name" : "机器视觉理论及应用"
	 * 出版编号  "num"  : "ISBN9787121153129"
	 * 出版社    "publisher"  : "电子工业出版社"
	 * 出版日期  "publishdate" : "20111220"
	 * 字数      "count"  : 40
	 * 类别		 "categoryid"  : "中文版专著"
	 * 得分 	 "score"  :  10
	 * 学期		 "submitdate"  : "2011-2012-1"
	 * 		}
	 * 	 	"1" : {
	 * 专著名称  "name" : "机器视觉理论及应用"
	 * 出版编号  "num"  : "ISBN9787121153129"
	 * 出版社    "publisher"  : "电子工业出版社"
	 * 出版日期  "publishdate" : "20111220"
	 * 字数      "count"  : 40
	 * 类别		 "categoryid"  : "中文版专著"
	 * 得分 	 "score"  :  10
	 * 学期		 "submitdate"  : "2011-2012-1"
	 * 	 	}
	 * }
	 */
	public function teachmonograph(){
		
			$gid = session('gid');
			if($gid === null){
				$this->ajaxReturn(0);
			}
			$teachmonograph = D('Teachbook');
			$teachmonograph_data = $teachmonograph -> getMonograph($gid);
			$this->ajaxReturn($teachmonograph_data);
	}
	/**
	 *	科研项目
	 *	"teachsearch_data" : {
	 *	"0" : {
	 *		科研项目名称  "name" 	: "MDF连续热压多场耦合效应机理及精准控制关键技术研究"
	 *		项目编号 	  "num"  	: "F201028"
	 *		学校财务编号  "finum"	: "41310301"
	 *		项目来源      "origin"  : "国家自然科学基金"
	 *		开始时间      "begindate"   : "20110101"
	 *		结束时间	  "enddate"  	: "20131212"
	 *		审批经费	  "permitfee" : 16.1
	 *		已到账经费    "getfee"    : 16.1
	 *		本年到账经费  "yearfee"	  : 16.1
	 *		本年到账时间  "yeardate"  : "20100930"
	 *		类别		  "category"  : "国家级科研课题"
	 *		本校排名	  "rank"      : 1
	 *		得分		  "score"     : 191
	 *		学期		  "submitdate" : "2010-2011-1"
	 *		是否首次拨款  "isfirstmoney" : 是
	 *	}	
	 *	"1" : {
	 *		
	 *	
	 *	}
	 *	}
	 */
	public function teachsearch(){
			$gid = session('gid');
			if($gid === null){
				$this->ajaxReturn(0);
			}
			$teachsearch = D('Teachsearch');
			$teachsearch_data = $teachsearch -> getTeachsearch($gid);
			$this->ajaxReturn($teachsearch_data);
	
	}
	/**
	 * 成果及知识产权
	 * "resultproperty_data" : {
	 * 			"0" : {
	 * 成果及知识产权名称	"name" : "森林资源消长数据采集与更新系统"
	 * 批准单位				"unit" : "中国版权保护中心"
	 * 证书编号				"certificateid" : "2011SR001015"
	 * 批准时间    			"permitdate"  : "20110110"
	 * 类别  				"categoryid"  : "软件登记"
	 * 得分     			"score"  : "25"
	 * 学期      			"submitdateid" : "2010-2011-1"
	 * 			}
	 * 			"1" : {
	 * 			
	 * 			}

	 * }
	 */
	public function resultproperty(){
	
			$gid = session('gid');
			if($gid === null){
				$this->ajaxReturn(0);
			}
			$resultproperty = D('Resultproperty');
			$resultproperty_data = $resultproperty -> getProperty($gid);
			$this->ajaxReturn($resultproperty_data);
	}
	/**
	 *优秀人才计划
	 * "talent_data" : {
	 * 		"0" : {
	 * 优秀人才计划名称  "name" : "教育部新世纪优秀人才支持计划"
	 * 批准单位          "unit" : "国家教育部"
	 * 批准时间 		 "permitdate" : "20121227"
	 * 类别       	     "categoryid" : "教育部新世纪优秀人才支持计划"
	 * 得分 			 "score"  : 50
	 * 学期				 "submitdate" : "2012-2013-1"
	 * 		}
	 * 		"1" : {
	 * 优秀人才计划名称  "name" : "黑龙江省师德师风先进个人"
	 * 批准单位          "unit" : "黑龙江省教育厅"
	 * 批准时间 		 "permitdate" : "20121227"
	 * 类别       	     "categoryid" : "省部级青年科技奖"
	 * 得分 			 "score"  : 50
	 * 学期				 "submitdate" : "2012-2013-2"
	 * 		}
	 * }
	 */
	public function talent(){
			$gid = session('gid');
			if($gid === null){
				$this->ajaxReturn(0);
			}
			$talent = D('Talent');
			$talent_data = $talent -> getTalent($gid);
			$this->ajaxReturn($talent_data);
	}
	/**
	 * 创新团队
	 * "innovationteam_data" : {
	 * 		"0" : {
	 * 创新团队名称  	 "name" : "创新团队"
	 * 批准单位          "unit" : "国家级科技创新团队"
	 * 批准时间 		 "permitdate" : "20121227"
	 * 类别       	     "categoryid" : "省部级青年科技奖"
	 * 得分 			 "score"  : 50
	 * 学期				 "submitdate" : "2012-2013-2"
	 * 		}
	 * 		"1" : {
	 * 创新团队名称  	 "name" : "创新团队"
	 * 批准单位          "unit" : "省级科技创新团队"
	 * 批准时间 		 "permitdate" : "20121227"
	 * 类别       	     "categoryid" : "省部级青年科技奖"
	 * 得分 			 "score"  : 50
	 * 学期				 "submitdate" : "2012-2013-2"
	 * 		}
	 * }
	 */
	public function innovationteam(){
		
			$gid = session('gid');
			if($gid === null){
				$this->ajaxReturn(0);
			}
			$innovationteam = D('Innovationteam');
			$innovationteam_data = $innovationteam -> getInnovationteam($gid);
			$this->ajaxReturn($innovationteam_data);

	}
	/**
	 * 科研论文及学术交流
	 * "searchthesis_data"  :  {
	 *				"0" : {
	 *	论文名称 		"name" :  "教学质量监控体系中教学方法的创新研究"
	 *	出版社			"publisher" : "教育学"
	 *	出版时间		"publishdate" : "20140501"
	 *	我校排名		"rank" : 1
	 * 	类别			"categoryid" : "教育教学研究论文"
	 * 	得分			"score"   :  20
	 * 	学期			"submitdate" : "2013-2014-2"
	 * 	索引号			"searchid"  : "231431434"
	 * 	期卷号			"chapterno" : "2014,5"
	 * 	起始页码		"startpage"  : "68"
	 * 	终止页码		"endpage"    :  "70"
	 *				}
	 *	 	"1" : {
	 *	 	
	 *	 	}
	 * }
	 */
	public function searchthesis(){
	
			$gid = session('gid');
			if($gid === null){
				$this->ajaxReturn(0);
			}
			$searchthesis = D('Thesis');
			$searchthesis_data = $searchthesis -> getTeachthesis($gid);
			$this->ajaxReturn($searchthesis_data);
		
	}

}