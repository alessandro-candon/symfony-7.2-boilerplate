package main

deny[msg] {
   envStaging := [ name | input[i].path == ".env.staging"; name := input[i].contents ]
   envProd := [ name | input[i].path == ".env.prod"; name := input[i].contents ]

   count(envStaging[0]) != count(envProd[0])

   msg = "Env staging and prod should contain the same number of keys"
}

#deny[msg] {
#
#   input[_].contents[i]
#
#   regex.match("/[A-z]*CLIENT[A-z]*|[A-z]*SECRET[A-z]*", i)
#
#   msg = "Envs should not contain clientId and Secret"
#}

