#
#<?php die('Forbidden.'); ?>
#Date: 2026-01-22 23:31:05 UTC
#Software: Joomla! 5.4.1 Stable [ Kutegemea ] 25-November-2025 16:00 GMT

#Fields: datetime	priority	clientip	category	message
2026-01-22T23:31:05+00:00	DEBUG	::1	yookassa	Start ЮKassa payment processing for reservation id 43
2026-01-22T23:31:05+00:00	DEBUG	::1	yookassa	Starting YooKassa payment for reservation ID: 43
2026-01-22T23:31:05+00:00	DEBUG	::1	yookassa	Creating YooKassa payment with data: {"amount":{"value":"500.00","currency":"RUB"},"confirmation":{"type":"redirect","return_url":"http:\/\/localhost\/Joomla_5.4.1\/index.php?option=com_solidres&task=reservation.finalize&reservation_id=43"},"capture":true,"description":"\u0411\u0440\u043e\u043d\u0438\u0440\u043e\u0432\u0430\u043d\u0438\u0435 \u211655fe47ed - \u0414\u0438\u0432\u043d\u0430\u044f \u0423\u0441\u0430\u0434\u044c\u0431\u0430","metadata":{"reservation_id":43,"reservation_code":"55fe47ed","asset_id":14},"receipt":{"customer":{"email":"grgerg@yahoo.com"},"items":[{"description":"\u0411\u0440\u043e\u043d\u0438\u0440\u043e\u0432\u0430\u043d\u0438\u0435 \u211655fe47ed - \u0414\u0438\u0432\u043d\u0430\u044f \u0423\u0441\u0430\u0434\u044c\u0431\u0430","quantity":1,"amount":{"value":"500.00","currency":"RUB"},"vat_code":1}]}}
2026-01-22T23:31:06+00:00	DEBUG	::1	yookassa	API Response (401): {
  "type" : "error",
  "id" : "019be80c-1dee-7fd2-8b7f-6ad874638be2",
  "description" : "Incorrect password format in the Authorization header. Use Secret key issued in Merchant Profile as the password",
  "parameter" : "Authorization",
  "code" : "invalid_credentials"
}
2026-01-22T23:31:06+00:00	DEBUG	::1	yookassa	YooKassa payment created with ID: 019be80c-1dee-7fd2-8b7f-6ad874638be2
2026-01-22T23:31:07+00:00	DEBUG	::1	yookassa	Finalizing YooKassa payment for reservation: 43
2026-01-22T23:31:08+00:00	DEBUG	::1	yookassa	API Response (401): {
  "type" : "error",
  "id" : "019be80c-2330-7ac9-8004-2ca616bd74b4",
  "description" : "Incorrect password format in the Authorization header. Use Secret key issued in Merchant Profile as the password",
  "parameter" : "Authorization",
  "code" : "invalid_credentials"
}
2026-02-05T14:06:46+00:00	DEBUG	::1	yookassa	Start ЮKassa payment processing for reservation id 44
2026-02-05T14:06:46+00:00	DEBUG	::1	yookassa	Starting YooKassa payment for reservation ID: 44
2026-02-05T14:06:46+00:00	DEBUG	::1	yookassa	Creating YooKassa payment with data: {"amount":{"value":"0.00","currency":"RUB"},"confirmation":{"type":"redirect","return_url":"http:\/\/localhost\/Joomla_5.4.1\/index.php?option=com_solidres&task=reservation.finalize&reservation_id=44"},"capture":true,"description":"\u0411\u0440\u043e\u043d\u0438\u0440\u043e\u0432\u0430\u043d\u0438\u0435 \u21168b1a5fed - \u0414\u0438\u0432\u043d\u0430\u044f \u0423\u0441\u0430\u0434\u044c\u0431\u0430","metadata":{"reservation_id":44,"reservation_code":"8b1a5fed","asset_id":14},"receipt":{"customer":{"email":"adslkcn'asdkc@yahoo.com"},"items":[{"description":"\u0411\u0440\u043e\u043d\u0438\u0440\u043e\u0432\u0430\u043d\u0438\u0435 \u21168b1a5fed - \u0414\u0438\u0432\u043d\u0430\u044f \u0423\u0441\u0430\u0434\u044c\u0431\u0430","quantity":1,"amount":{"value":"0.00","currency":"RUB"},"vat_code":1}]}}
2026-02-05T14:06:48+00:00	DEBUG	::1	yookassa	API Response (401): {
  "type" : "error",
  "id" : "019c2e20-827e-7a99-9fda-ec0fe6972e17",
  "description" : "Incorrect password format in the Authorization header. Use Secret key issued in Merchant Profile as the password",
  "parameter" : "Authorization",
  "code" : "invalid_credentials"
}
2026-02-05T14:06:48+00:00	DEBUG	::1	yookassa	YooKassa payment created with ID: 019c2e20-827e-7a99-9fda-ec0fe6972e17
2026-02-05T14:06:49+00:00	DEBUG	::1	yookassa	Finalizing YooKassa payment for reservation: 44
2026-02-05T14:06:50+00:00	DEBUG	::1	yookassa	API Response (401): {
  "type" : "error",
  "id" : "019c2e20-8c96-734e-9973-0b36f21c4e74",
  "description" : "Incorrect password format in the Authorization header. Use Secret key issued in Merchant Profile as the password",
  "parameter" : "Authorization",
  "code" : "invalid_credentials"
}
