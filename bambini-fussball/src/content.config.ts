import { defineCollection, z } from 'astro:content';

const articleSchema = z.object({
  title: z.string(),
  description: z.string(),
  pubDate: z.date(),
  updatedDate: z.date().optional(),
  author: z.string().default('Redaktion'),
});

const trainer = defineCollection({
  type: 'content',
  schema: articleSchema,
});

const eltern = defineCollection({
  type: 'content',
  schema: articleSchema,
});

const vereine = defineCollection({
  type: 'content',
  schema: articleSchema,
});

export const collections = { trainer, eltern, vereine };
