var express = require('express');
var router = express.Router()
var loginRouter = require('./loginService')
var storeRouter = require('./storeService')

router.use((req, res, next) => {
    console.log("Called: ", req.path)
    next()
})

router.use(loginRouter)
router.use(storeRouter)

module.exports = router
