package main

import (
	"bytes"
	"encoding/json"
	"io"
	"net/http"
	"os"
	"testing"
	"time"

	"github.com/stretchr/testify/suite"
)

type RegressionTestSuite struct {
	suite.Suite
	baseURL string
	client  *http.Client
}

func (suite *RegressionTestSuite) SetupSuite() {
	suite.baseURL = os.Getenv("TEST_BASE_URL")
	if suite.baseURL == "" {
		suite.baseURL = "http://localhost:8000"
	}
	
	suite.client = &http.Client{
		Timeout: 30 * time.Second,
	}
	
	// Wait for server to be ready
	suite.waitForServer()
}

func (suite *RegressionTestSuite) waitForServer() {
	maxRetries := 30
	for i := 0; i < maxRetries; i++ {
		resp, err := suite.client.Get(suite.baseURL + "/api/health")
		if err == nil && resp.StatusCode == 200 {
			resp.Body.Close()
			return
		}
		if resp != nil {
			resp.Body.Close()
		}
		time.Sleep(1 * time.Second)
	}
	suite.T().Fatal("Server not ready after 30 seconds")
}

func (suite *RegressionTestSuite) makeRequest(method, path string, body interface{}) (*http.Response, error) {
	var reqBody io.Reader
	if body != nil {
		jsonBody, err := json.Marshal(body)
		if err != nil {
			return nil, err
		}
		reqBody = bytes.NewBuffer(jsonBody)
	}
	
	req, err := http.NewRequest(method, suite.baseURL+path, reqBody)
	if err != nil {
		return nil, err
	}
	
	req.Header.Set("Content-Type", "application/json")
	req.Header.Set("Accept", "application/json")
	
	return suite.client.Do(req)
}

func TestRegressionSuite(t *testing.T) {
	suite.Run(t, new(RegressionTestSuite))
}