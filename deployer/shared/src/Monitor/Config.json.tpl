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

    "db_hostname" : "{{db.host}}",
    "db_username" : "{{db.username}}",
    "db_password" : "{{db.password}}",
    "db_name"     : "{{db.name}}",
    "db_driver"   : "pdo_mysql",

    "ms_in_hour": 3600,
    "ms_in_day": 86400
}
