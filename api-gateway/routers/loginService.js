var express = require('express');
var crypto = require('crypto');
var router = express.Router()
const apiAdapter = require('./apiAdapter')
const isAuthorized = require('../controller/requestAuthenticator')

const BASE_URL = 'http://nginx'
const api = apiAdapter(BASE_URL)
var ApiToken = require('../model/apitoken');


router.post('/login', (req, res) => {
  api.post(req.path, req.body).then(resp => {
    jwt_token =  resp.data['token']

    opaque_token = crypto.createHash('sha1').update(jwt_token).digest('hex')

  ApiToken.create({
    jwt: jwt_token,
    opaque: opaque_token
  }, (err, user) => {
    if (err) return res.status(500).send("Save token failed")

    res.status(200).send({api_key: opaque_token})
  })

  }, (err, resp) => {
    if (err) return res.status(500).json({error: 'login failed'})
  })
})

router.post('/auth-echo', isAuthorized, (req, res) => {
  api.post(req.path, req.body, {headers: {'Authorization': req.headers.authorization}}).then(resp => {
    res.send(resp.data)
  }, (err, resp) => {
    if (err) return res.status(500).json({error: 'no access!'})
  })
})

router.post('/api-gateway-echo', isAuthorized, (req, res) => {
  res.send("Api Key found!")
})

module.exports = router
