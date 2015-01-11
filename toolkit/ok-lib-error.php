<?php
function ok_handleErrorAndThrowException($aNumber, $aMessage, $aFile, $aLine) {
  throw new ErrorException($aMessage, 0, $aNumber, $aFile, $aLine);
}