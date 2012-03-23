package org.aerialframework.rpc.operation
{
	import mx.rpc.AbstractOperation;
	import mx.rpc.AsyncResponder;
	import mx.rpc.AsyncToken;
	import mx.rpc.events.FaultEvent;
	import mx.rpc.events.ResultEvent;

	import org.aerialframework.rpc.AbstractService;

	public class Operation implements IOperation
    {

        private var _service:AbstractService;
        private var _method:String;
        private var _resultHandler:Function;
        private var _faultHandler:Function;
        private var _tokenData:Object;
        private var _token:AsyncToken;
        private var _op:AbstractOperation;
        private var _args:Array;

        public function Operation(service:AbstractService, remoteMethod:String, ...args)
        {
            _service = service;
            _method = remoteMethod;
            _op = service.getOperation(_method);
            _args = args;
        }

        public function callback(resultHandler:Function, faultHandler:Function = null, tokenData:Object = null):Operation
        {
            _faultHandler = faultHandler;
            _resultHandler = resultHandler;
            _tokenData = tokenData;
            return this;
        }

        private function notifyResultHandler(event:ResultEvent, token:Object = null):void
        {
            event.preventDefault();
            if(token)
            {
                _resultHandler(event, token);
            }
            else
            {
                _resultHandler(event);
            }
        }

        private function notifyFaultHandler(event:FaultEvent, token:Object = null):void
        {
            if(_faultHandler != null)
            {
                event.preventDefault();
                if(token)
                {
                    _faultHandler(event, token);
                }
                else
                {
                    _faultHandler(event);
                }
            }
        }

        public function execute():AsyncToken
        {
			_service.endpoint = AbstractService.SERVER_URL;

            return  _execute();
        }

        private function _execute():AsyncToken
        {
            _token = _op.send(_args);

            if(_resultHandler !== null) _token.addResponder(new AsyncResponder(notifyResultHandler, notifyFaultHandler, _tokenData));

            return _token;
        }
    }
}