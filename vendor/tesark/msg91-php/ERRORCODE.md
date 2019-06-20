# [MSG91 SERVICE ERROR CODE](http://control.msg91.com/apidoc/errorCode/errorCodes.php) 

### SEND SMS
 [Msg91 Send SMS](http://api.msg91.com/apidoc/textsms/send-sms.php) 

### SEND OTP
[Msg91 Send OTP](http://api.msg91.com/apidoc/sendotp/send-otp.php)

# Error Codes
### Missing parameters
| Error code | Description |
| ---------- | ------------- |
| 101		 | Missing mobile no.
| 102		 | Missing message
| 103	 	 | Missing sender ID
| 104		 | Missing username
| 105		 | Missing password
| 106		 | Missing Authentication Key
| 107		 | Missing Route
### Invalid parameters
| Error code |	Description|
| ---------- | ------------|
| 202		 | Invalid mobile number. You must have entered either less than 10 digits or there is an alphabetic character in the mobile number field in API.
| 203		 | Invalid sender ID. Your sender ID must be 6 characters, alphabetic.
| 207		 | Invalid authentication key. Crosscheck your authentication key from your account’s API section.
| 208		 | IP is blacklisted. We are getting SMS submission requests other than your whitelisted IP list.
### Error codes
| Error code | 	Description |
| ---------- | ------------|
| 205		 | This route is dedicated for high traffic. You should try with minimum 20 mobile numbers in each request
| 209		 | Default Route for dialplan not found
| 210		 | Route could not be determined
| 301		 | Insufficient balance to send SMS
| 302		 | Expired user account. You need to contact your account manager.
| 303		 | Banned user account
| 306		 | This route is currently unavailable. You can send SMS from this route only between 9 AM - 9 PM.
| 307		 | Incorrect scheduled time
| 308		 | Campaign name cannot be greater than 32 characters
| 309		 | Selected group(s) does not belong to you
| 310		 | SMS is too long. System paused this request automatically.
| 311		 | Request discarded because same request was generated twice within 10 seconds
| 418		 | IP is not whitelisted
| 505		 | Your account is a demo account. Please contact support for details
| 506		 | Small campaign limit exceeded. (only 20 campaigns of less than 100 SMS in 24 hours can be sent, exceeding it will show the error)
### System errors
| Error code| 	Description |
| ---------- | ------------|
| 001		 | Unable to connect database
| 002		 | Unable to select database
| 601		 | Internal error.Please contact support for details