# ChangeLog - Aliyun OSS SDK for PHP

## v2.2.4 / 2017-04-25

* fix getObject to local file bug

## v2.2.3 / 2017-04-14

* fix md5 check

## v2.2.2 / 2017-01-18

* Fix the connection count and memory bugs in php7

## v2.2.1 / 2016-12-01

* Disable auto filling Accept-Encoding header by http curl.

## v2.2.0 / 2016-11-22

* Fix PutObject/CompleteMultipartUpload's return value issue.(#26)

## v2.1.0 / 2016-11-12

* Add [RTMP](https://help.aliyun.com/document_detail/44297.html) APIs
* Add Image service support(https://help.aliyun.com/document_detail/44686.html)

## v2.0.7 / 2016-06-17

* Support append object

## v2.0.6

* Trim access key id/secret and endpoint
* Refine tests and setup travis CI

## v2.0.5

* Add Add/Delete/Get BucketCname APIs

## v2.0.4

* Add Put/Get Object Acl APIs

## v2.0.3

* Fix Util class's constant definition error in old PHP versions (version < 5.6)

## v2.0.2

* Fix the issue of no way to specify Content-Type in a multipart upload.

## v2.0.1

* Add special characters' handling in ListObjects/ListMultipartUploads
* Provide detail error message in OSSException.


## 2015.11.25

* **Major version upgrade. It's not compatible with older version. The new version is much better in usability. All users are recommended to upgrade to this version.**

## Updated

* No longer support PHP 5.2

### Newly added changes

* Introduce the name space
* Correct the API naming. Use CamelCased naming convention.
* API's input parameter change. Extract the common parameters out of parameter Options
* API's return result update. The return result now is processed and easy to use by callers.
* OssClient constructor update
* Support CName and IP based endpoint
* Reorganize the sample file's structure. Now it's grouped by functions
* Add configuration settings for connection timeout, socket timeout
* Remove the obsolete APIs about Object Group
* Use English in all messages in the OssException.

### Bug Fixes

* object name check is incomplete
