FAQ
===
**I'm sure I have entered the correct email and SMTP settings but I get SMTP connect() failed. Why?**

This seems to happen on connections with long responsetime, >100ms. I'm not really sure whos fult this is or if there is a workaround. Meanwhile, you could debug some more with `GET /test/mail/2` api, see api docs.
