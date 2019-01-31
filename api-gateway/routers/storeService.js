var express = require('express');
var router = express.Router()
const apiAdapter = require('./apiAdapter')
const isAuthorized = require('../controller/requestAuthenticator')

const store_service_api = apiAdapter('http://nginx:8080')


router.post('/stores-echo', isAuthorized, (req, res) => {
  store_service_api.post(req.path, req.body, {headers: {'Authorization': req.headers.authorization}}).then(resp => {
    res.send(resp.data)
  }, (err, resp) => {
    if (err) return res.status(500).json({error: 'no access!'})
  })
})

router.post('/stores-protected-data', isAuthorized, (req, res) => {
  store_service_api.post(req.path, req.body, {headers: {'Authorization': req.headers.authorization}}).then(resp => {
    res.send(resp.data)
  }, (err, resp) => {
    if (err) return res.status(500).json({error: 'no access!'})

  })
})

module.exports = router
