var express = require('express');
var router = express.Router()
const apiAdapter = require('./apiAdapter')
const isAuthorized = require('../controller/requestAuthenticator')

const BASE_URL = 'http://nginx'
const api = apiAdapter(BASE_URL)


router.post('/login', (req, res) => {
  api.post(req.path, req.body).then(resp => {
    res.send(resp.data)
  }, (err, resp) => {
    if (err) return res.status(500).json({error: 'login failed'})
  })
})

router.get('/v1*', (req, res) => {
  api.get(req.path).then(resp => {
    res.send(resp.data)
  })
})

module.exports = router
