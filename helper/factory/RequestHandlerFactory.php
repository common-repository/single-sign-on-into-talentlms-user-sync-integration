<?php

namespace MSTUSI\Helper\Factory;

interface RequestHandlerFactory
{
	public function generateRequest();

	public function getRequestType();
}