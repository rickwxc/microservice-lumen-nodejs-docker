//var jwt = require('jsonwebtoken');
var config = require('../config')

var ApiToken = require('../model/apitoken');

module.exports = (req, res, next) => {
  if (!req.headers['api_key']) {
    res.status(401).send("Unauthorized")
  } else {

    ApiToken.findOne({ opaque: req.headers['api_key'] } , (err, apiToken) => {
      if (err) return res.status(500).send("Internal server error")
      if (!apiToken) return res.status(404).send("api key not found.")

			//put back saved jwt token into the request header
      req.headers.authorization = "Bearer " + apiToken['jwt'];

      next()
    })
  }
}
