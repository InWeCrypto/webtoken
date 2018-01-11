DROP TABLE IF EXISTS public.admins;

CREATE TABLE public.admins (
  id         SERIAL PRIMARY KEY NOT NULL, --管理员信息表
  name       VARCHAR(100)       NOT NULL, --登录账号
  nickname   VARCHAR(100), --昵称
  password   VARCHAR(60),
  img        VARCHAR(100), --头像
  ip         VARCHAR(40), --最后一次登录ip
  p_id       INT                NOT NULL DEFAULT 0, --0表示是超级管理员
  role_id    INT                NOT NULL DEFAULT 0, --角色ID
  is_valid   BOOLEAN            NOT NULL DEFAULT TRUE, --是否允许登录
  created_at TIMESTAMP,
  updated_at TIMESTAMP

)


DROP TABLE IF EXISTS public.roles;
CREATE TABLE public.roles (
  id         SERIAL PRIMARY KEY NOT NULL, --角色表
  name       VARCHAR(50)        NOT NULL, --角色名称
  is_valid   BOOLEAN            NOT NULL DEFAULT TRUE,
  module_ids VARCHAR(255), --模块id
  created_at TIMESTAMP,
  updated_at TIMESTAMP
)

DROP TABLE IF EXISTS public.modules;
CREATE TABLE public.modules (
  id         SERIAL PRIMARY KEY NOT NULL, --模块配置表
  name       VARCHAR(50)        NOT NULL, --模块名称
  p_id       INT                NOT NULL DEFAULT 0,
  uri        VARCHAR(50),
  created_at TIMESTAMP,
  updated_at TIMESTAMP
)

DROP TABLE IF EXISTS public.user_wallet_categories;

CREATE TABLE public.user_wallet_categories (
  id          SERIAL PRIMARY KEY NOT NULL, --用户关联钱包类型
  user_id     BIGINT             NOT NULL,
  category_id BIGINT             NOT NULL, --钱包类型,1ETH,2BTC,3代币,4导入钱包
  name        VARCHAR(30)        NOT NULL,
  created_at  TIMESTAMP,
  updated_at  TIMESTAMP
);

DROP TABLE IF EXISTS public.wallet_categories;

CREATE TABLE public.wallet_categories (
  id          SERIAL PRIMARY KEY NOT NULL, --钱包类型
  name        VARCHAR(30)        NOT NULL,
  created_at  TIMESTAMP,
  updated_at  TIMESTAMP
);

DROP TABLE IF EXISTS public.gnt_categories;

CREATE TABLE public.gnt_categories (
  id          SERIAL PRIMARY KEY NOT NULL, --代币类型
  category_id BIGINT             NOT NULL, --钱包类型
  name        VARCHAR(30)        NOT NULL, --代币名称
  created_at  TIMESTAMP,
  updated_at  TIMESTAMP
);


DROP TABLE IF EXISTS public.user_gnt_categories;

CREATE TABLE public.user_gnt_categories (
  id              SERIAL PRIMARY KEY NOT NULL, --用户所选代币类型
  user_id         BIGINT             NOT NULL, --用户ID
  gnt_category_id BIGINT             NOT NULL, --代币ID
  name            VARCHAR(30)        NOT NULL, --代币名称
  created_at      TIMESTAMP,
  updated_at      TIMESTAMP
);


DROP TABLE IF EXISTS public.users;

CREATE TABLE public.users (
  id         SERIAL PRIMARY KEY NOT NULL, --用户表
  open_id    VARCHAR(100),
  password   VARCHAR(60),
  nickname   VARCHAR(100), --用户昵称
  sex        SMALLINT           NOT NULL DEFAULT 1, --1男,2女
  img        VARCHAR(100),
  province   VARCHAR(100),
  city       VARCHAR(100),
  country    VARCHAR(100),
  created_at TIMESTAMP,
  updated_at TIMESTAMP
);


DROP TABLE IF EXISTS public.articles;

CREATE TABLE public.articles (
  id         SERIAL PRIMARY KEY NOT NULL, --文章表
  detail     JSONB,
  created_at TIMESTAMP,
  updated_at TIMESTAMP
);


DROP TABLE IF EXISTS public.ico_categories;

CREATE TABLE public.ico_categories (
  id         SERIAL PRIMARY KEY NOT NULL, --ico类表
  name       VARCHAR(100),
  img        VARCHAR(100), --icon图片地址
  created_at TIMESTAMP,
  updated_at TIMESTAMP
);

DROP TABLE IF EXISTS public.wallets;
CREATE TABLE public.wallets (
  id          SERIAL PRIMARY KEY NOT NULL, --钱包表
  user_id     BIGINT             NOT NULL,
  category_id BIGINT             NOT NULL, --钱包类型
  name        VARCHAR(255)        NOT NULL, --钱包名称
  address     VARCHAR(255)       NOT NULL, --钱包地址
  created_at  TIMESTAMP,
  updated_at  TIMESTAMP
);


DROP TABLE IF EXISTS public.user_contacts;
CREATE TABLE public.user_contacts (
  id          SERIAL PRIMARY KEY NOT NULL, --通讯录表
  user_id     BIGINT             NOT NULL,
  category_id BIGINT             NOT NULL, --钱包类型
  name        VARCHAR(255)       NOT NULL, --联系人姓名
  address     VARCHAR(255)       NOT NULL, --钱包地址
  remark      TEXT, --备注
  created_at  TIMESTAMP,
  updated_at  TIMESTAMP
);

DROP TABLE IF EXISTS public.monetary_units;
CREATE TABLE public.monetary_units (
  id          SERIAL PRIMARY KEY NOT NULL, --货币单位表
  name        VARCHAR(255)       NOT NULL, --名称
  created_at  TIMESTAMP,
  updated_at  TIMESTAMP
);

DROP TABLE IF EXISTS public.user_monetary_units;
CREATE TABLE public.user_monetary_units (
  id               SERIAL PRIMARY KEY NOT NULL, --用户已选择货币单位表
  user_id          BIGINT             NOT NULL, --用户ID
  monetary_unit_id BIGINT             NOT NULL, --货币单位ID
  created_at       TIMESTAMP,
  updated_at       TIMESTAMP
);

DROP TABLE IF EXISTS public.wallet_orders;
CREATE TABLE public.wallet_orders (
  id              SERIAL PRIMARY KEY NOT NULL, --钱包交易记录表
  wallet_id       BIGINT             NOT NULL, --钱包ID
  user_id         BIGINT             NOT NULL, --支付用户ID
  trade_no        VARCHAR(255)       NOT NULL, --交易单号
  pay_address     VARCHAR(255)       NOT NULL, --支付账号
  receive_address VARCHAR(255)       NOT NULL, --收款账号
  own_address     VARCHAR(255)       NOT NULL, --所属地址
  block_number    VARCHAR(100)       NULL,
  remark          TEXT, --备注
  fee             VARCHAR(255)       NOT NULL, --交易金额
  handle_fee      VARCHAR(255), --手续费
  hash            VARCHAR(255), --唯一识别码
  status          SMALLINT           NOT NULL  DEFAULT 1, --状态,0交易失败,1准备打包2打包中,3交易成功,
  finished_at     TIMESTAMP, --到账时间
  created_at      TIMESTAMP,
  updated_at      TIMESTAMP
);


DROP TABLE IF EXISTS public.market_categories;
CREATE TABLE public.market_categories (
  id         SERIAL PRIMARY KEY NOT NULL, --行情类型表
  name       VARCHAR(255)       NOT NULL, --行情名称
  flag       VARCHAR(255),
  token      VARCHAR(255),
  url        VARCHAR(255),
  created_at TIMESTAMP,
  updated_at TIMESTAMP
);


DROP TABLE IF EXISTS public.user_market_categories;
CREATE TABLE public.user_market_categories (
  id         SERIAL PRIMARY KEY NOT NULL, --行情类型表
  market_id  BIGINT,
  user_id    BIGINT,
  created_at TIMESTAMP,
  updated_at TIMESTAMP
);

DROP TABLE IF EXISTS public.market_notifications;
CREATE TABLE public.market_notifications (
  id          SERIAL PRIMARY KEY NOT NULL, --行情提醒表
  market_id   BIGINT,
  user_id     BIGINT,
  upper_limit VARCHAR(255), --上限
  lower_limit VARCHAR(255), --下限
  created_at  TIMESTAMP,
  updated_at  TIMESTAMP
);

ALTER TABLE public.user_gnt_categories ADD wallet_id BIGINT NULL;
COMMENT ON COLUMN public.user_gnt_categories.wallet_id IS '钱包ID';

DROP TABLE IF EXISTS ico;
create table ico
(
	id	SERIAL PRIMARY KEY NOT NULL ,--ico列表
	title	VARCHAR(255),
	img VARCHAR(255),
	intro VARCHAR(255),
	start_at	TIMESTAMP,
	end_at TIMESTAMP,--开始/结束时间用于判断ico进行状态,如果开始时间为null,则表示待确定
	cny	VARCHAR(100),--众筹对象对象,币种
	block_net VARCHAR(255),--区块网络
	address VARCHAR(255),--合约地址
	url VARCHAR(255),
	created_at	TIMESTAMP,
	updated_at	TIMESTAMP
);
DROP TABLE IF EXISTS public.ico_orders;
CREATE TABLE public.ico_orders (
  id              SERIAL PRIMARY KEY NOT NULL, --ico交易记录表
  wallet_id       BIGINT             NOT NULL, --钱包ID,用于关联查询
  ico_id          BIGINT             NOT NULL, --icoID
  user_id         BIGINT             NOT NULL, --支付用户ID
  trade_no        VARCHAR(255)       NOT NULL, --交易单号
  pay_address     VARCHAR(255)       NOT NULL, --支付账号
  receive_address VARCHAR(255)       NOT NULL, --收款账号
  own_address     VARCHAR(255)       NOT NULL, --所属单号
  block_number    VARCHAR(100)       NULL,
  remark          TEXT, --备注
  fee             VARCHAR(255)       NOT NULL, --交易金额
  handle_fee      VARCHAR(255), --手续费
  cny             VARCHAR(255),--币种
  hash            VARCHAR(255), --唯一识别码
  status          SMALLINT           NOT NULL  DEFAULT 1, --状态,0交易失败,1打包中,2交易成功,
  finished_at     TIMESTAMP, --到账时间
  created_at      TIMESTAMP,
  updated_at      TIMESTAMP
);
ALTER TABLE public.ico ADD is_valid SMALLINT NULL;
COMMENT ON COLUMN public.ico.is_valid IS '0审核失败,1审核中,2审核成功';
ALTER TABLE public.ico ADD is_show SMALLINT DEFAULT 1 NULL;
COMMENT ON COLUMN public.ico.is_show IS '是否显示,0否,1是';


ALTER TABLE public.wallets ADD deleted_at TIMESTAMP NULL;

ALTER TABLE public.gnt_categories ADD icon VARCHAR(255) NULL;
ALTER TABLE public.gnt_categories ADD address VARCHAR(255) NULL;
ALTER TABLE public.gnt_categories ADD gas VARCHAR(255) NULL;

ALTER TABLE public.wallet_categories ADD icon VARCHAR(255) NULL;
ALTER TABLE public.wallet_categories ADD address VARCHAR(255) NULL;
ALTER TABLE public.wallet_categories ADD gas VARCHAR(255) NULL;

DROP TABLE IF EXISTS public.messages;
CREATE TABLE public.messages(
  id SERIAL PRIMARY KEY NOT NULL ,--消息表
  user_id BIGINT,
  title VARCHAR(255) ,
  content text,
  type SMALLINT NOT NULL DEFAULT 0,--0未读,1已读
  ext SMALLINT NOT NULL DEFAULT 0,
  hash VARCHAR(255) ,
  created_at  TIMESTAMP,
  updated_at TIMESTAMP
);
ALTER TABLE public.messages ADD resource_type VARCHAR(255) DEFAULT 'SYSTEM' NULL;
COMMENT ON COLUMN public.messages.resource_type IS 'ICO_ORDER,WALLET_ORDER,ICO_ARTICLE,SYSTEM';
ALTER TABLE public.messages ADD resource_id INT DEFAULT 0 NULL;

ALTER TABLE public.wallet_orders ADD flag VARCHAR(100) NULL;

ALTER TABLE public.market_categories ADD source VARCHAR(100) DEFAULT 'coinmarket' NULL;
ALTER TABLE public.wallet_orders ADD block_number VARCHAR(100) NULL;
DROP TABLE IF EXISTS public.configs;
CREATE TABLE public.configs (
  id         SERIAL PRIMARY KEY,
  name       VARCHAR(255),
  value      VARCHAR(255),
  created_at TIMESTAMP,
  updated_at TIMESTAMP
);

ALTER TABLE public.market_notifications ADD currency VARCHAR(100) NULL;
COMMENT ON COLUMN public.market_notifications.currency IS '币种';

ALTER TABLE public.wallets ADD address_hash160 VARCHAR(100) NULL;
