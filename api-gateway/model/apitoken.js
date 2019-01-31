var mongoose = require('mongoose')

var ApiTokenSchema = new mongoose.Schema({
    opaque: String,
    jwt: String
})

mongoose.model('ApiToken', ApiTokenSchema)

module.exports = mongoose.model('ApiToken')
