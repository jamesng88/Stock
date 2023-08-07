CREATE TABLE stock(
    stockid int PRIMARY KEY,
    stockname varchar(20) not null,
    fullname varchar(70) not null,
    stocktype varchar(50) not null,
    market varchar(30) not null
);

CREATE TABLE dividend(
    stockid int not null,
    announced_date date not null,
    finance_year int not null,    
    dividendsubject varchar(40) not null,
    ex_date date not null,
    payment_date date not null,
    amount float not null,
   	PRIMARY KEY(stockid, ex_date),
   	FOREIGN KEY(stockid) REFERENCES stock(stockid)
);

CREATE TABLE customer(
    userid int AUTO_INCREMENT PRIMARY KEY,
    username varchar(20) not null,
   	password varchar(20) not null
);

CREATE TABLE trade(
    tradeid int AUTO_INCREMENT PRIMARY KEY,
    stockid int not null,
    userid int not null,
   	trade_date date not null,
    quantity int not null,
   	price float not null,
   	tradetype boolean not null,
   	FOREIGN KEY(stockid) REFERENCES stock(stockid),
   	FOREIGN KEY(userid) REFERENCES customer(userid)
);

CREATE TABLE storage(
    stockid int not null,
    userid int not null,
    ex_date date not null,
    amount int not null,
    FOREIGN KEY(stockid) REFERENCES stock(stockid),
    FOREIGN KEY(userid) REFERENCES customer(userid),
    FOREIGN KEY(ex_date) REFERENCES dividend(ex_date),
);
