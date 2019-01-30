var express = require('express');
var router = express.Router()
var loginRouter = require('./loginService')
var authRouter = require('../controller/AuthController')

router.use((req, res, next) => {
    console.log("Called: ", req.path)
    next()
})

router.use(loginRouter)
router.use(authRouter)

module.exports = router
