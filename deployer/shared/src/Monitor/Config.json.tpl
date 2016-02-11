 {
    "notification": {
        "data": {
            "mail_to":"admin@localhost"
        },
        "services": [
            "Mail\\FakeSimple",
            "Mail\\FakeSmtp"
        ]
    },

    "notification_delay_in_hours" : 1,
    "history_expire_time_in_days" : 7,

    "format" : "json",

    "hostname" : "{{db.host}}",
    "username" : "{{db.username}}",
    "password" : "{{db.password}}",
    "database" : "{{db.name}}",
    "dbdriver" : "mysql"
}
