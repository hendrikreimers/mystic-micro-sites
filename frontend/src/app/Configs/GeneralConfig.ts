
/**
 * General config values
 */
export const GeneralConfig = {
  noLoginButtonOnRoutes: <string[]>['/login', '/', '/logout'],

  // Used in ImageProcessor
  allowedMimeTypes: <string[]>[
    'image/jpeg'
  ],

  // Used in linkField Component
  urlExpr: <string>'^(https?:\\/\\/)((([a-zA-Z0-9$-_@.&+!*"(),]|(%[0-9a-fA-F][0-9a-fA-F]))+(\\:[a-zA-Z0-9$-_@.&+!*"()' +
    ',]|(%[0-9a-fA-F][0-9a-fA-F]))*@)?(([0-9a-zA-Z]([0-9a-zA-Z-]{0,61}[0-9a-zA-Z])?\\.)+[a-zA-Z]{2,6}\\.?|[0-9a-fA-F]' +
    '{1,4}:[0-9a-fA-F]{1,4}:[0-9a-fA-F]{1,4}:[0-9a-fA-F]{1,4}:[0-9a-fA-F]{1,4}:[0-9a-fA-F]{1,4}:[0-9a-fA-F]{1,4}:[0-9' +
    'a-fA-F]{1,4}|localhost|(([0-9]{1,3}\\.){3}[0-9]{1,3}))(\\:[0-9]{1,5})?((\\/?)|([\\/\\?]\\S+))?)$'
}
