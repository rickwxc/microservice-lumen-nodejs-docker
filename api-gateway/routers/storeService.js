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

router.post(/v1\/stores/, isAuthorized, (req, res) => {
  store_service_api.post(req.path, req.body, {headers: {'Authorization': req.headers.authorization}}).then(resp => {
    res.send(resp.data)
  }, (err, resp) => {
    if (err) return res.status(err.response.status).json(err.response.data)
  })
})

router.get(/v1\/stores/, isAuthorized, (req, res) => {
  store_service_api.get(req.url, {headers: {'Authorization': req.headers.authorization}}).then(resp => {
    res.send(resp.data)
  }, (err, resp) => {
    if (err) return res.status(err.response.status).json(err.response.data)

  })
})


module.exports = router
