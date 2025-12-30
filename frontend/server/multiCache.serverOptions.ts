import { defineMultiCacheOptions } from 'nuxt-multi-cache/dist/runtime/serverOptions'
import redisDriver from 'unstorage/drivers/redis'
import memoryDriver from 'unstorage/drivers/memory'

const { redis } = useRuntimeConfig()
const redisOptions = {
  port: parseInt(redis.port),
  host: redis.host,
  username: redis.username,
  password: redis.password,
  tls: {
    host: redis.host
  }
}

export default defineMultiCacheOptions({
  data: {
    storage: {
      driver: process.env.NODE_ENV === 'development'
        ? memoryDriver()
        : redisDriver({
          base: 'data:',
          ...redisOptions
        })
    }
  }
})
