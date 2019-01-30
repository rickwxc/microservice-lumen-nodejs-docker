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

router.post('/echo', isAuthorized, (req, res) => {
  res.send("Authenticate successfully!")
})

module.exports = router
