<?php
include("DbConnection.php");

// $admin = "CREATE TABLE admintb
// (
//     AdminID int not null primary key auto_increment,
//     AdminFullName varchar(30),
//     AdminUserName varchar(30),
//     AdminProfile varchar(255),
//     AdminEmail varchar(30),
//     AdminPassword varchar(30),
//     AdminPhone varchar(30),
//     AdminPosition varchar(30),
//     SignupDate varchar(30),
//     AdminStatus varchar(30)
// )";

// try {
//     $query = mysqli_query($connect, $admin);
//     echo "Data Successfully saved";
// } catch (mysqli_sql_exception) {
//     echo "Data has been saved";
// }

// $customer = "CREATE TABLE customertb
// (
//     CustomerID int not null primary key auto_increment,
//     FullName varchar(30),
//     UserName varchar(30),
//     CustomerEmail varchar(30),
//     CustomerPassword varchar(30),
//     CustomerPhone varchar(30),
//     CustomerBirthday varchar(30),
//     SignupDate varchar(30)
// )";

// try {
//     $query = mysqli_query($connect, $customer);
//     echo "Data Successfully saved";
// } catch (mysqli_sql_exception) {
//     echo "Data has been saved";
// }

// $producttype = "CREATE TABLE producttypetb
// (
//     ProductTypeID int NOT NULL Primary Key Auto_Increment,
//     ProductTypeName varchar(30)
// )";

// try {
//     $query = mysqli_query($connect, $producttype);
//     echo "Data Successfully saved";
// } catch (mysqli_sql_exception) {
//     echo "Data has been saved";
// }

// $product = "CREATE TABLE producttb
// (
//     ProductID int not null primary key auto_increment,
//     ProductTypeID int,
//     Title varchar(255),
//     img1 varchar(255),
//     img2 varchar(255),
//     img3 varchar(255),
//     Price decimal(10, 2),
//     DiscountPrice decimal(10, 2),
//     Color varchar(30),
//     ProductDetail varchar(255),
//     Brand varchar(30),
//     ModelHeight varchar(30),
//     ProductSize varchar(30),
//     LookAfterMe varchar(255),
//     AboutMe varchar(255),
//     ExtendedSizing varchar(30),
//     MoreColors varchar(30),
//     SellingFast varchar(30),
//     AddedDate varchar(30),
//     Stock int,
//     FOREIGN KEY (ProductTypeID) REFERENCES producttypetb (ProductTypeID)
// )";

// try {
//     $query = mysqli_query($connect, $product);
//     echo "Data Successfully saved";
// } catch (mysqli_sql_exception) {
//     echo "Data has been saved";
// }

// $favorite = "CREATE TABLE favoritetb
// (
//     FavoriteID int not null primary key auto_increment,
//     CustomerID int,
//     ProductID int,
//     FOREIGN KEY (CustomerID) REFERENCES customertb (CustomerID),
//     FOREIGN KEY (ProductID) REFERENCES producttb (ProductID)
// )";

// try {
//     $query = mysqli_query($connect, $favorite);
//     echo "Data Successfully saved";
// } catch (mysqli_sql_exception) {
//     echo "Data has been saved";
// }

// $cart = "CREATE TABLE carttb
// (
//     CartID int not null primary key auto_increment,
//     CustomerID int,
//     ProductID int,
//     Size varchar(30),
//     Quantity int,
//     FOREIGN KEY (CustomerID) REFERENCES customertb (CustomerID),
//     FOREIGN KEY (ProductID) REFERENCES producttb (ProductID)
// )";

// try {
//     $query = mysqli_query($connect, $cart);
//     echo "Data Successfully saved";
// } catch (mysqli_sql_exception) {
//     echo "Data has been saved";
// }

// $contact = "CREATE TABLE cuscontacttb
// (
//     ContactID int not null primary key auto_increment,
//     CustomerEmail varchar(30),
//     ContactMessage varchar(255),
//     ContactDate varchar(30),
//     Status varchar(30)
// )";

// try {
//     $query = mysqli_query($connect, $contact);
//     echo "Data Successfully saved";
// } catch (mysqli_sql_exception) {
//     echo "Data has been saved";
// }

// $review = "CREATE TABLE reviewtb
// (
//     ReviewID int not null primary key auto_increment,
//     CustomerID int,
//     ProductID int,
//     Rating int,
//     Comment varchar(65535),
//     ReviewDate varchar(30),
//     FOREIGN KEY (CustomerID) REFERENCES customertb (CustomerID),
//     FOREIGN KEY (ProductID) REFERENCES producttb (ProductID)
// )";

// try {
//     $query = mysqli_query($connect, $review);
//     echo "Data Successfully saved";
// } catch (mysqli_sql_exception) {
//     echo "Data has been saved";
// }


// $supplier = "CREATE TABLE suppliertb
// (
//     SupplierID int not null primary key auto_increment,
//     SupplierName varchar(30),
//     SupplierEmail varchar(30),
//     SupplierPassword varchar(30),
//     SupplierPhone varchar(30),
//     SupplierAddress varchar(255),
//     AddedDate varchar(30)
// )";

// try {
//     $query = mysqli_query($connect, $supplier);
//     echo "Data Successfully saved";
// } catch (mysqli_sql_exception) {
//     echo "Data has been saved";
// }

// $purchase = "CREATE TABLE purchasetb
// (
//     PurchaseID varchar(30) not null primary key,
//     AdminID int,
//     SupplierID int,
//     TotalAmount decimal(10, 2),
//     PurchaseTax decimal(10, 2),
//     Status varchar(30),
//     PurchaseDate varchar(30),
//     FOREIGN KEY (AdminID) REFERENCES admintb (AdminID),
//     FOREIGN KEY (SupplierID) REFERENCES suppliertb (SupplierID)
// )";

// try {
//     $query = mysqli_query($connect, $purchase);
//     echo "Data Successfully saved";
// } catch (mysqli_sql_exception) {
//     echo "Data has been saved";
// }

// $purchasedetail = "CREATE TABLE purchasedetailtb
// (
//     PurchaseID varchar(30) not null,
//     ProductID int not null,
//     PurchaseUnitQuantity int,
//     PurchaseUnitPrice decimal(10, 2),
//     PurchaseDate varchar(30),
//     PRIMARY KEY (PurchaseID, ProductID),
//     FOREIGN KEY (PurchaseID) REFERENCES purchasetb(PurchaseID),
//     FOREIGN KEY (ProductID) REFERENCES producttb(ProductID)
// )";

// try {
//     $query = mysqli_query($connect, $purchasedetail);
//     echo "Data Successfully saved";
// } catch (mysqli_sql_exception) {
//     echo "Data has been saved";
// }

// $order = "CREATE TABLE ordertb
// (
//     OrderID varchar(30) not null primary key,
//     CustomerID int,
//     CustomerPhone varchar(30),
//     ShippingAddress varchar(255),
//     City varchar(30),
//     State varchar(30),
//     PaymentMethod varchar(30),
//     TotalPrice decimal(10, 2),
//     OrderTax decimal(10, 2),
//     TotalAmount decimal(10, 2),
//     Remark varchar(255),
//     OrderDate varchar(30),
//     Status varchar(30),
//     FOREIGN KEY (CustomerID) REFERENCES customertb (CustomerID)
// )";

// try {
//     $query = mysqli_query($connect, $order);
//     echo "Data Successfully saved";
// } catch (mysqli_sql_exception) {
//     echo "Data has been saved";
// }

// $orderdetail = "CREATE TABLE orderdetailtb
// (
//     OrderID varchar(30) not null,
//     ProductID int not null,
//     OrderUnitQuantity int,
//     PurchaseUnitPrice decimal(10, 2),
//     PRIMARY KEY (OrderID, ProductID),
//     FOREIGN KEY (OrderID) REFERENCES ordertb(OrderID),
//     FOREIGN KEY (ProductID) REFERENCES producttb(ProductID)
// )";

// try {
//     $query = mysqli_query($connect, $orderdetail);
//     echo "Data Successfully saved";
// } catch (mysqli_sql_exception) {
//     echo "Data has been saved";
// }
