# ChangeLog - Aliyun OSS SDK for PHP

## 2015.11.25

** 大版本升级，不再兼容以前接口，新版本对易用性做了很大的改进，建议用户迁移到新版本。 **

## 修改内容

* 不再支持PHP 5.2版本

### 新增内容

* 引入命名空间
* 接口命名修正，采用驼峰式命名
* 接口入参修改，把常用参数从Options参数中提出来
* 接口返回结果修改，对返回结果进行处理，用户可以直接得到容易处理的数据结构　
* OssClient的构造函数变更
* 支持CNAME和IP格式的Endpoint地址
* 重新整理sample文件组织结构，使用function组织功能点
* 增加设置连接超时，请求超时的接口
* 去掉Object Group相关的已经过时的接口
* OssException中的message改为英文

### 问题修复

* object名称校验不完备