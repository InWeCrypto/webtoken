# API
## 1. API接口通信规定
- 接口采用 **HTTPS**, **RESTFUL** 协议
- 请求返回数据格式
	#### 所有数据返回都基于以下的 **JSON** 协议 

	>失败时返回:
	
		{
			"code":请求结果编码,
			"msg":提示信息,
			"url":4007跳转目标路径,
			"data":返回信息正文
		}
- 备注:
	所有接口请求模块都是在"api"下,如登录:
	>www.xxx.com(/index.php)/api/index/login
	
	>"字段说明"和字段子集说明"是两个概念,"字段子集说明"表示的是该字段下面是集合.
	
	>除通用模块外,其他模块均需要传递登录返回的token字段.
	
### 编码对照:

 	4000	请求执行成功
	4001	未登陆
	4002	无权限
	4003	路由不存在
	4004	验证不通过
	4005	查询数据不存在
	4006	请求执行失败
	4007	请求执行成功,即将跳转	
	4008	未注册
	4009	token过期

	
## 2.   接口详细说明  
### 2.1(仅注册与登录接口不需要添加header ct)
#### 2.1.1  注册与登录[post]    auth
`请求参数:`
	
	登录:
	open_id			string
	
	注册:
	app_id				string			
	secret				string
	js_code			string
	grant_type		string
		
`成功返回:`
    
    token				string			有效时间,2周,拼接在header里面,键名:ct
    user				array			当前用户信息
    
    user字段说明:
    
    	open_id		string
    	nickname		string			
    	sex				int				1男,2女
    	img				string			头像路径
    	
    
    备注:其他需要返回的信息待定
    
`失败返回:`


#### 2.1.2  文件上传[get]    upload
`请求参数:`
    
	file            resource        文件资源
    
`成功返回:`
    
	uri             string          文件保存地址,服务器相对路径
	url             string          绝对路径,可用于显示
			
        
`失败返回:`


#### 2.1.3  钱包类型[get]    wallet-category
`请求参数:`
    
    
`成功返回:`
    
	id				int					钱包类型ID
	name			string				类型名称
	gas				stirng
	icon			string
	address		string				合约地址		
        
`失败返回:`


#### 2.1.5  用户添加钱包类型[post]    user-wallet
`请求参数:`
    
    category_ids		json			钱包类型id
    
`成功返回:`
    		
        
`失败返回:`

#### 2.1.6  代币类型[get]    gnt-category
`请求参数:`

	wallet_category_id		int			钱包类型ID
	wallet_id				int			钱包ID
    
`成功返回:`
    
	id				int					代币类型ID
	category_id		int				钱包类型ID
	name			string				代币名称
	gas				stirng
	icon			string
	address		string				合约地址		
        
`失败返回:`


#### 2.1.7  用户已添加代币类型列表[get]   user-gnt
`请求参数:`

	wallet_category_id		int			钱包类型ID
	wallet_id					int			钱包ID
    
    
`成功返回:`

	list			array
	
	list字段子集说明:
    
		id				int					用户代币类型ID
		gnt_category_id		int				代币类型ID
		name			string				代币名称		
        
`失败返回:`

#### 2.1.7_1  用户添加代币[post]   user-gnt
`请求参数:`

	wallet_id		int			钱包ID
	gnt_category_ids	json	代币类型,如:[1,2,3]  
    
`成功返回:`
        
`失败返回:`

#### 2.1.8  用户置顶已添加代币类型[put]   user-gnt/{:id}
`请求参数:`

	用户代币类型ID
    
    
`成功返回:`
		
        
`失败返回:`

#### 2.1.8  删除已添加代币类型[delete]   user-gnt/{:id}
`请求参数:`

	用户代币类型ID
    
    
`成功返回:`
		
        
`失败返回:`


#### 2.1.9  发现[get]   find
`请求参数:`
    
`成功返回:`

	banner			array			
	ico				array
	
	banner字段子集说明:
	
		id			int			对应文章ID
		detail		array		详情
		
		detail字段说明:
		
			title		string
			img			string		
			desc		string		简介
	
	ico字段子集说明:
		
		id			int			
		name		string
		img			string			icon图标路径
		
		
	备注:文章列表因为涉及到分页,所以单独请求文章列表接口			
        
`失败返回:`


#### 2.1.10  文章列表[get]   article
`请求参数:`

	page			int				页码,默认1
	per_page		int				每页显示条数,默认5
    
`成功返回:`

	list			array			
	
	list字段子集说明:
	
		id			int			文章ID
		detail		array		详情
		created_at	string		发表时间
		
		detail字段说明:
		
			title		string
			img			string		
			desc		string		简介
			content		string		文章内容
        
`失败返回:`


#### 2.1.11  文章详情[get]   article/{:id}
`请求参数:`
    
`成功返回:`
	
	id			int			对应文章ID
	detail		array		详情
	created_at	string		发表时间
	
	detail字段说明:
	
		title		string
		img			string		
		desc		string		简介
		content		string		文章内容
        
`失败返回:`


#### 2.1.12  通讯录列表[get]   contact
`请求参数:`
    
`成功返回:`
	
	list			array
	
	list字段子集说明:
	
		id			int			联系人记录ID
		category_id		int		对应钱包类型
		name		string			名称
		address		string			钱包地址
		remark		string			备注
		wallet		array		如果该钱包地址未关联到具体的钱包记录,则该值为null
		
		wallet字段说明:
		
			user	array		
			
				img			string			联系人头像路径
				
        
`失败返回:`

#### 2.1.13  添加通讯录[post]   contact
`请求参数:`
    
    category_id		int			对应钱包类型
    name			string			名称
    address		string			钱包地址
    remark		string			备注(可选项)
    
`成功返回:`
	
	        
`失败返回:`

#### 2.1.14  通讯录详情[get]   contact/{:id}
`请求参数:`
    
    
`成功返回:`
	
	id			int			联系人记录ID
	category_id		int		对应钱包类型
	name		string			名称
	address		string			钱包地址
	remark		string			备注
	wallet		array		如果该钱包地址未关联到具体的钱包记录,则该值为null
	
	wallet字段说明:
	
		user	array		
		
			img			string			联系人头像路径
	        
`失败返回:`


#### 2.1.15  编辑通讯录信息[put]   contact/{:id}
`请求参数:`
    
    category_id		int			对应钱包类型
    name			string			名称
    address		string			钱包地址
    remark		string			备注(可选项)
    
`成功返回:`
	
	id			int			联系人记录ID
	category_id		int		对应钱包类型
	name		string			名称
	address		string			钱包地址
	remark		string			备注
	wallet		array		如果该钱包地址未关联到具体的钱包记录,则该值为null
	
	wallet字段说明:
	
		user	array		
		
			img			string			联系人头像路径
	        
`失败返回:`


#### 2.1.16  删除通讯录[delete]   contact/{:id}
`请求参数:`
    
    
`成功返回:`
	
	        
`失败返回:`

#### 2.1.17  添加钱包[post]   wallet
`请求参数:`
    
    category_id			int				钱包类型ID
    name					string			钱包名称
    address				string			钱包地址
    
`成功返回:`
	
	record					array			
	
	record字段说明:
	
		id					int				钱包id
		category_id		int				钱包类型ID
		name				string			钱包名称
		address			string			钱包地址
		created_at		string			创建时间
	        
`失败返回:`

#### 2.1.17_1  钱包列表[get]   wallet
`请求参数:`
    
    
`成功返回:`
	
	list					array			
	
	list字段说明:
	
		id					int				钱包id
		category_id		int				钱包类型ID
		name				string			钱包名称
		address			string			钱包地址
		created_at		string			创建时间
		category			array			钱包类型信息
		
		category字段说明:
		
			name			string			钱包类型
	        
`失败返回:`

#### 2.1.17_2  修改钱包名称[put]   wallet/{:id}
`请求参数:`
    
    name					string			钱包名称
    
`成功返回:`
	
	        
`失败返回:`

#### 2.1.17_3  删除钱包[delete]   wallet/{:id}
`请求参数:`
    
      
`成功返回:`
	
	        
`失败返回:`

#### 2.1.18  钱包订单[get]   wallet-order
`请求参数:`
    	
    wallet_id				int				钱包ID
    flag					string			钱包/代币类型:如eth
    
`成功返回:`
	
	list				array
	
	list字段子集说明:
	
		id				int					订单ID
		user_id		int					付款人ID
		wallet_id		int					钱包ID
		trade_no		string				交易单号
		pay_address		string				付款地址
		receive_address		stirng			收款地址
		remark			string				备注
		fee				string				交易金额
		handle_fee		string				手续费
		hash			strig
		status			int					0交易失败,1准备打包2打包中,3交易成功
		flag			string			钱包/代币类型:如eth
		finished_at		string			交易完成时间
		created_at		string			订单创建时间
	        
`失败返回:`


#### 2.1.19  创建钱包订单[post]   wallet-order
`请求参数:`
    	 	
	wallet_id				int			钱包ID
	data				string			rawtransaction那个字符串
	pay_address		string				付款地址
	receive_address		stirng			收款地址
	remark			string				备注(可选参数)
	fee				string				交易金额
	handle_fee		string				手续费
	flag				string			钱包/代币类型:如eth

    
`成功返回:`
	
	        
`失败返回:`

#### 2.1.20  钱包订单详情[get]   wallet-order/{:id}
`请求参数:`
    	
    
`成功返回:`
	
	id				int					订单ID
	user_id		int					付款人ID
	wallet_id		int					钱包ID
	trade_no		string				交易单号
	pay_address		string				付款地址
	receive_address		stirng			收款地址
	remark			string				备注
	fee				string				交易金额
	handle_fee		string				手续费
	hash			strig
	status			int					0失败,1打包中,2交易成功
	flag					string			钱包/代币类型:如eth
	finished_at		string			交易完成时间
	created_at		string			订单创建时间
	        
`失败返回:`

#### 2.1.21  获取货币单位及用户选择情况[get]   monetary-unit
`请求参数:`
    	
    
`成功返回:`
	
	list			array
	
	list字段子集说明:
		
		id				int					货币单位ID
		name			string				货币单位名称
		user_unit_count	int					0表示用户未选择,1表示用户已选择
		created_at		string				创建时间	
	        
`失败返回:`

#### 2.1.22  变更用户选择的货币单位[post]   monetary-unit
`请求参数:`

	monetary_unit_id		int				货币单位ID    	
    
`成功返回:`
	
	        
`失败返回:`

#### 2.1.23  用户信息[get]   user/create
`请求参数:`

    
`成功返回:`
	
	user			array
	
	user字段说明:
	
		img			string				头像
		nickname		string			昵称/名称
		sex			int					性别,1男,2女
	        
`失败返回:`

#### 2.1.23.1  修改用户信息[post]   user
`请求参数:`

	nickname			string				用户名称
	img					string				用户头像
    
`成功返回:`
	
	        
`失败返回:`

#### 2.1.24  用户已添加的行情分类列表[get]   market-category
`请求参数:`

	is_all				int				可选参数,当传递该值得时候返回的是所有的行情分类列表
    
`成功返回:`
	
	list				array
	
	list字段子集说明:
	
		id			int				行情分类ID
		name		string			网站名称
		flag		string			行情标记,用于显示以及关联行情实时数据
		token		string			网站token
		url			string			网站url
		relation_user_count int		0表示没选择,1表示已选择
		created_at	string			创建时间
		is_all不存在是有以下字段:	
		relation_cap	array		最新交易信息
		relation_cap_min		array		最高最低相关信息
		
	
		relation_cap字段说明:
			price_usd			string			当前兑换美元价格
			price_btc			string			当前兑换btc价格
			volume_usd_24h	string			24小时内美元交易总量
			market_cap_usd	string
			available_supply	string
			total_supply		string
			percent_change_1h	string
			percent_change_24h	string
			percent_change_7d	strng
			last_updated		string			更新时间,时间戳
			price_cny			string			当前兑换人民币价格
			volume_cny_24h		string		
			market_cap_cny		string
			
		relation_cap_min	字段说明:
		
			price_usd_first		string		0点开盘美刀成交价格
			price_usd_last		string		当前美刀成交价格
			price_usd_low		string			当天美刀成交最低价
			price_usd_high		string		当天美刀成交最高价
			timestamp				string		时间戳,更新时间
			//其他字段类似	
	        
`失败返回:`

#### 2.1.25  添加行情分类[post]   market-category
`请求参数:`

	market_ids		json				行情分类id,eg:[1,2,3]   
    
`成功返回:`
	
	        
`失败返回:`

#### 2.1.26  行情详情[get]   market-category/{:id}
`请求参数:`

	start			string			开始时间,默认当天凌晨
	end				string			结束时间,默认当前服务器时间
	type			int				类型,默认1,支持1 分,2 时,3 天,4 周
    
`成功返回:`
	
	record				array		
	list				array 		图表相关信息
	
	record字段子集说明:
	
		id			int				行情分类ID
		name		string			网站名称
		flag		string			行情标记,用于显示以及关联行情实时数据
		token		string			网站token
		url			string			网站url
		created_at	string			创建时间
		is_all不存在是有以下字段:	
		relation_cap	array		最新交易信息
		relation_cap_min		array		最高最低相关信息
	
	
		relation_cap字段说明:
			price_usd			string			当前兑换美元价格
			price_btc			string			当前兑换btc价格
			volume_usd_24h	string			24小时内美元交易总量
			market_cap_usd	string
			available_supply	string
			total_supply		string
			percent_change_1h	string
			percent_change_24h	string
			percent_change_7d	strng
			last_updated		string			更新时间,时间戳
			price_cny			string			当前兑换人民币价格
			volume_cny_24h		string		
			market_cap_cny		string
			
		relation_cap_min	字段说明:
		
			price_usd_first		string		0点开盘美刀成交价格
			price_usd_last		string		当前美刀成交价格
			price_usd_low		string			当天美刀成交最低价
			price_usd_high		string		当天美刀成交最高价
			timestamp				string		时间戳,更新时间
			//其他字段类似
			
	list字段子集说明:
	
			price_usd_first		string		0点开盘美刀成交价格
			price_usd_last		string		当前美刀成交价格
			price_usd_low		string			当天美刀成交最低价
			price_usd_high		string		当天美刀成交最高价
			
			//其他字段类似
		
		
	        
`失败返回:`


#### 2.1.27  用户行情提醒列表[get]   market-notification
`请求参数:`
    
`成功返回:`
	
	list				array
	
	list字段子集说明:
	
		id			int				行情分类ID
		name		string			网站名称
		flag		string			行情标记,用于显示以及关联行情实时数据
		token		string			网站token
		url			string			网站url
		created_at	string			创建时间
		relation_notification_count	int			用户是否已添加提醒,0表示没有添加
		relation_notification	array		用户提醒信息, relation_notification_count为0是,本字段返回空数组
		relation_cap			array		最新行情信息
		
		relation_notification字段子集说明:
			
			upper_limit		string		上限
			lower_limit		string		下限
			
		relation_cap字段说明:
			
			price_usd			string			当前兑换美元价格
			price_btc			string			当前兑换btc价格
			volume_usd_24h	string			24小时内美元交易总量
			market_cap_usd	string
			available_supply	string
			total_supply		string
			percent_change_1h	string
			percent_change_24h	string
			percent_change_7d	strng
			last_updated		string			更新时间,时间戳
			price_cny			string			当前兑换人民币价格
			volume_cny_24h		string		
			market_cap_cny		string
				
			        
`失败返回:`

#### 2.1.28  添加用户行情提醒[post]   market-notification
`请求参数:`
		
	market_arr		json			数据格式:[{"market_id":11,"upper_limit":11,"lower_limit":222}];其中market_id为行情id	
	currency			string			当前用户选择的法币币种(usd或cny)
    
`成功返回:`

			        
`失败返回:`

#### 2.1.28.1  修改用户行情提醒[put]   market-notification/{:id}
`请求参数:`
	
	upper_limit		string		上限
	lower_limit		string		下限	
	    
`成功返回:`

			        
`失败返回:`

#### 2.1.28.2  删除用户行情提醒[delete]   market-notification/{:id}
`请求参数:`	
	    
`成功返回:`

			        
`失败返回:`

#### 2.1.29  获取货币单位及用户选择情况[get]   monetary-unit
`请求参数:`
		    
`成功返回:`

	list			array
	
	list字段子集说明:
		
		id			int				货币单位ID
		name		string
		created_at	string 		创建时间
		user_unit_count		int		0表示用户没选择当前货币单位
			        
`失败返回:`

#### 2.1.30  变更用户选择的货币单位[post]   monetary-unit
`请求参数:`

	monetary_unit_id		int			货币单位ID
		    
`成功返回:`
			        
`失败返回:`


#### 2.1.31  ico列表[get]   ico
`请求参数:`

	page			int				默认1
	per_page		int				分页数,默认10
		    
`成功返回:`

	list			array
	
	list字段子集说明:
	
		id			int				icoID
		title		string			标题
		img			string			
		intro		string			简介
		start_at	string			开始时间,用于列表页面tag显示判断
		end_at		string
		cny			stirng			币种
		block_net	string			区块网络
		address		string			合约地址
		url			string			详情url,直接访问url
			        
`失败返回:`

#### 2.1.32  ico订单列表[get]   ico-order
`请求参数:`

	page			int				默认1
	per_page		int				分页数,默认10
		    
`成功返回:`

	
	list				array
	
	list字段子集说明:
	
		id				int					订单ID
		user_id		int					付款人ID
		wallet_id		int					钱包ID
		trade_no		string				交易单号
		pay_address		string				付款地址
		receive_address		stirng			收款地址
		fee				string				交易金额
		handle_fee		string				手续费
		hash			strig
		created_at		string			订单创建时间
		ico				array				ico信息
		
		ico字段说明:
		
			title		string			标题	
			cny			stirng			币种
			        
`失败返回:`



#### 2.1.33  创建ico订单[post]   ico-order
`请求参数:`

	wallet_id			int				钱包ID
	ico_id				int				ICOID
	trade_no			string			交易单号
	pay_address			stirng		支付钱包地址
	receive_address		string		收款/合约地址
	fee					string			支付金额
	handle_fee		string			手续费
	hash				string			交易hash
	
	//备注:这里支付金额,手续费都没有带单位,单位沿用的ico详情的cny字段
		    
`成功返回:`

				        
`失败返回:`



#### 2.1.34  ico订单详情[get]   ico-order/{:id}
`请求参数:`
		    
`成功返回:`

	record				array
	
	record字段说明:
	
		id				int					订单ID
		user_id		int					付款人ID
		wallet_id		int					钱包ID
		trade_no		string				交易单号
		pay_address		string				付款地址
		receive_address		stirng			收款地址
		fee				string				交易金额
		handle_fee		string				手续费
		hash			strig
		created_at		string			订单创建时间
		ico				array				ico信息
		
		ico字段说明:
		
			title		string			标题	
			cny			stirng			币种
				        
`失败返回:`


#### 2.1.35  gas详情(用于ico参投)[get]   gas
`请求参数:`

	category_id		int				钱包/gnt类型
	type				int				0钱包,1gnt,默认钱包
		    
`成功返回:`

	record				array
	
	record字段说明:
	
		name			string				代币名称
		gas				stirng
		icon			string
		address		string				合约地址
				        
`失败返回:`


#### 2.1.36  获取sts[get]   sts
`请求参数:`

`成功返回:`

	RequestId			string
	AssumedRoleUser		array		通过扮演角色接口获取的临时身份
	Credentials		array			访问凭证
	
	AssumedRoleUser字段说明:
	
			AssumedRoleId		string		该角色临时身份的用户ID
			Arn			string			该角色临时身份的资源描述符
	
	Credentials字段说明:
	
			AccessKeySecret		string		访问密钥

				        
`失败返回:`


#### 2.1.37  获取文章详情[get]   article/{:id}
`请求参数:`

`成功返回:`

	html页面

				        
`失败返回:`


####注意:以下扩展接口统一使用post方式,另:[参考地址](https://docs.google.com/spreadsheets/d/1-H-D_PXrIinVCl4CFLGJWfVz4AsjIWktyuy88uY2O90/edit#gid=0)(我也不知道个字段含义)

#### 2.1.38  获取gas价格[post]   extend/getGasPrice
`请求参数:`

`成功返回:`
				        
`失败返回:`

#### 2.1.39  获取用户交易次数[post]   extend/getTransactionCount
`请求参数:`
	
	address
	
`成功返回:`
				        
`失败返回:`


#### 2.1.40 发送签名后的交易[post]   extend/sendRawTransaction
`请求参数:`
	
	data
	
`成功返回:`
				        
`失败返回:`


#### 2.1.41 获得交易详情[post]   extend/getTransaction
`请求参数:`
	
	txHash
	
`成功返回:`
				        
`失败返回:`

#### 2.1.42 获得账户余额(ETH)[post]   extend/getBalance
`请求参数:`
	
	address
	
`成功返回:`
				        
`失败返回:`

#### 2.1.43 获得代币余额(Tokens)[post]   extend/balanceOf
`请求参数:`

	contract
	address
	
`成功返回:`
				        
`失败返回:`



#### 2.1.44 获得代币总发行量[post]   extend/totalSupply
`请求参数:`

	contract
	
`成功返回:`
				        
`失败返回:`


#### 2.1.45 代币转账ABI[post]   extend/transferABI
`请求参数:`

	contract
	to
	value
	
`成功返回:`
				        
`失败返回:`

#### 2.1.46 交易市场价格[post]   extend/priceList
`请求参数:`
	
`成功返回:`
				        
`失败返回:`

#### 2.1.47 当前最大块号[post]   extend/blockNumber
`请求参数:`
	
`成功返回:`
				        
`失败返回:`

#### 2.1.48 获得当前块的发生速度（每秒多少块）,用于评估众筹开始时间[post]   extend/blockPerSecond
`请求参数:`
	
`成功返回:`
				        
`失败返回:`


#### 2.1.49 获取事务[post]   extend/getTransactions
`请求参数:`
	
	from
	to
	address
	
`成功返回:`
				        
`失败返回:`

#### 2.1.50 获取事务详情[post]   extend/getTransactionById
`请求参数:`
	
	txid
	
`成功返回:`
				        
`失败返回:`


#### 2.1.51 查询费率[post]   extend/estimateFee
`请求参数:`
	
	nbBlocks
	
`成功返回:`
				        
`失败返回:`

#### 2.1.52 查询余额[post]   extend/address
`请求参数:`
	
	address
	
`成功返回:`
				        
`失败返回:`

#### 2.1.51 getEstimateGas[post]   extend/getEstimateGas
`请求参数:`
	
	to				string			转账目标地址
	
`成功返回:`
				        
`失败返回:`

#### 2.1.53 钱包余额及市场行情[get]   conversion
`请求参数:`
	
	wallet_ids    json			钱包ID(批量查询)
	
`成功返回:`

	list			array
	
	list字段子集说明:
	
		balance		string		钱包余额(16进制)
		category		array
			
		category字段说明:
			
			name		string		钱包类型
			cap			array		钱包类型当前行情
			
			cap字段说明:
			
				price_usd		string		美刀
				price_btc		string		btc
				price_cny		string		人民币
				        
`失败返回:`

#### 2.1.54 钱包代币列表余额及市场行情[get]   conversion/{:wallet_id}
`请求参数:`
	
`成功返回:`

	list			array
	
	list字段子集说明:
	
		balance		string		代币余额(16进制)
		gnt_category		array
			
		gnt_category字段说明:
			
			name		string		代币名称
			icon		string		代币图标路径
			cap			array		钱包类型当前行情
			
			cap字段说明:
			
				price_usd		string		美刀
				price_btc		string		btc
				price_cny		string		人民币
				        
`失败返回:`


#### 2.1.55  消息列表[get]   message
`请求参数:`

	type			int				0未读,1已读,默认0
	page			int				页码,默认1
	per_page		int				每页显示条数,默认10
    
`成功返回:`

	list			array			
	
	list字段子集说明:
	
		id			int			消息ID
		title		string
		content		string		
		created_at	string
		    
`失败返回:`


#### 2.1.56  消息标记已读[put]   message/{:id}
`请求参数:`

	    
`成功返回:`

			    
`失败返回:`


#### 2.1.57  删除消息[delete]   message/{:id}
`请求参数:`

	    
`成功返回:`

			    
`失败返回:`

#### 2.1.58  获取最小块高[get]   min-block
`请求参数:`

	    
`成功返回:`

	min_block_num 	string
			    
`失败返回:`

#### 2.1.59  移动推送说明

推送都是按ACCOUNT方式推送,故在注册的时候使用open_id作为注册id.

推送信息扩展字段:

	resource_type		string			ICO_ORDER Ico订单,WALLET_ORDER 钱包订单(包括钱包里的代币转账),ICO_ARTICLE Ico众投文章,SYSTEM 系统消息,MARKET  行情信息   有对应详情的跳对应详情 没有的,比如系统消息,跳消息列表
	
	resource_id		int				跳转详情的ID